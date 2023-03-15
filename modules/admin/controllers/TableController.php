<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\controllers;

use Yii;
use app\modules\admin\models\Table;
use app\modules\admin\models\forms\TableForm;
use yii\web\NotFoundHttpException;
use app\models\ExtensionsList;

use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;

use yii\web\UploadedFile;

use yii\web\Response;

class TableController extends BaseController
{
    public $model = Table::class;
    public $modelForm = TableForm::class;

    public function actionIndex()
    {
        $result = ExtensionsList::getTableList();
        $dataProvider = [];

        if (!empty($result)) {
            $dataProvider = array();

            foreach ($result as $key => $val) {
                $dataProvider[$key]['table_name'] = $val;
            }

            if (Yii::$app->getRequest()->getQueryParam('table_name')) {
                $name = Yii::$app->getRequest()->getQueryParam('table_name');

                $dataProvider = array_filter($dataProvider, function ($item) use ($name) {
                    echo $item['table_name'] . '<br>';

                    return (!empty($name) && ($item['table_name'] == $name) ? true : false);
                });
            }
        }

        $searchModel = ['table_name'];

        $provider = new ArrayDataProvider([
            'allModels' => $dataProvider,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $provider,
            'searchModel' => $searchModel,
            'fullData' => $dataProvider
        ]);
    }

    public function actionUpdate($id)
    {
        $modelClass = $this->model;
        $modelClassForm = $this->modelForm;

        $model = $modelClass::GetTableInfo($id);

		//echo "<pre>"; print_r($model); die;

        if (!$model) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        foreach ($model->constraints as $constraintKey => $constraint) {
            if ($constraint['type'] == 'FOREIGN KEY') {
                $ref_table_name = $constraint['ref_table_name'];
                $col_list = ExtensionsList::getTableColumns($ref_table_name);
                $col_list = arrayhelper::map($col_list, function ($data) {
                    return "{$data['column_name']}";
                }, 'column_name');
                $col_list = array_map('strtolower', $col_list);

                $model->constraints[$constraintKey]['col_list'] = $col_list;
            }
        }

        $tables = ExtensionsList::getTableList();
        $dataTypes = ExtensionsList::getDataTypes();
        $dataTypes = arrayhelper::map($dataTypes, function ($data) {
            return "{$data['type']}";
        }, 'type');

		//echo "<pre>"; print_r($dataTypes); die;

        if ($post = Yii::$app->request->post('TableForm')) {
            $postData = Yii::$app->request->post('TableForm');

            $final_arr = array('table_name' => $postData['table_name']);

            foreach ($postData['columns'] as $postKey => $postVal) {
                if (array_key_exists($postKey, $model->columns) && array_key_exists($postKey, $postData['columns'])) {
                    $oldName = $model->columns[$postKey]['name'];
                    $newName = $postData['columns'][$postKey]['name'];

                    if ($oldName != $newName) {
                        $renameData = array("name" => $oldName, "new_name" => $newName, "operation" => "RENAME");
                        $final_arr['columns'][] = $renameData;
                    }

                    $model_nullable = ($model->columns[$postKey]['nullable'] == 0) ? 'false' : 'true';

                    if ($model->columns[$postKey]['type'] != strtolower($postVal['type']) || $model_nullable != $postVal['nullable'] || $model->columns[$postKey]['scale'] != $postVal['scale'] || $model->columns[$postKey]['length'] != $postVal['length']) {
                        $modifyData = array(
                            "name" => $oldName,
                            "type" => $postVal['type'],
                            "nullable" => $postVal['nullable'],
                            "scale" => $postVal['scale'],
                            "length" => $postVal['length'],
                            'operation' => 'MODIFY'
                        );
                        $final_arr['columns'][] = $modifyData;
                    }
                } else {
                    end($model->columns);
                    $key = key($model->columns);

                    if ($postKey > $key) {
                        $addData = array(
                            "name" => $postVal['name'],
                            "type" => $postVal['type'],
                            "nullable" => $postVal['nullable'],
                            "scale" => $postVal['scale'],
                            "length" => $postVal['length'],
                            "operation" => "ADD"
                        );
                        $final_arr['columns'][] = $addData;
                    }
                }
            }

            $droppedCols = array_diff_key($model->columns, $postData['columns']);

            foreach ($droppedCols as $col) {
                $name = $col['name'];

                $dropData = array('name' => $name, 'operation' => 'DROP');
                $final_arr['columns'][] = $dropData;
            }

            foreach ($postData['constraints'] as $postKey => $postVal) {
                if (array_key_exists($postKey, $model->constraints) && array_key_exists($postKey,
                        $postData['constraints'])) {
                    $oldName = $model->constraints[$postKey]['name'];
                    $newName = $postData['constraints'][$postKey]['name'];
                } else {
                    end($model->constraints);
                    $key = key($model->constraints);

                    if ($postKey > $key) {
                        if ($postVal['type'] == 'PRIMARY KEY') {
                            $addData = array(
                                "name" => $postVal['name'],
                                "type" => $postVal['type'],
                                "ref_columns" => $postVal['ref_columns'],
                                "operation" => "ADD"
                            );
                        } else {
                            $addData = array(
                                "name" => $postVal['name'],
                                "type" => $postVal['type'],
                                "ref_table_name" => $postVal['ref_table_name'],
                                "columns" => $postVal['columns'],
                                "ref_columns" => $postVal['ref_columns'],
                                "operation" => "ADD"
                            );
                        }

                        $final_arr['constraints'][] = $addData;
                    }
                }
            }

            $droppedCols = array_diff_key($model->constraints, $postData['constraints']);

            foreach ($droppedCols as $col) {
                $name = $col['name'];

                $dropData = array(
                    'type' => $col['type'],
                    'name' => $name,
                    "columns" => array($col['columns']),
                    'operation' => 'DROP'
                );
                $final_arr['constraints'][] = $dropData;
            }

            if (!empty($final_arr['columns']) || !empty($final_arr['constraints'])) {
                $response = Table::updateTable($final_arr);

                if ($response['requestresult'] == 'unsuccessfully') {
                    Yii::$app->getSession()->setFlash('danger', Yii::t('app', $response['extendedinfo']));
                } else {
                    Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Successfully update'));
                }
            }

            $model = $modelClass::GetTableInfo($id);
        }

        return $this->render('update', ['model' => $model, 'tables' => $tables, 'dataTypes' => $dataTypes]);
    }

    public function actionCreate()
    {
        /* @var $modelClass Table */
        $modelClass = $this->model;
        $modelClassForm = $this->modelForm;
        $model = new $modelClassForm();

        $tables = ExtensionsList::getTableList();
        $dataTypes = ExtensionsList::getDataTypes();
        $dataTypes = ArrayHelper::map($dataTypes, function ($data) {
            return "{$data['type']}";
        }, 'type');

        if ($post = Yii::$app->request->post($model->formName(), false)) {
            $response = Table::createTable(Yii::$app->request->post($model->formName()));

            if ($response['requestresult'] == 'unsuccessfully') {
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Successfully create'));
            } else {
                Yii::$app->getSession()->setFlash('danger', Yii::t('app', $response['extendedinfo']));
            }
        }

        return $this->render('create', ['model' => $model, 'tables' => $tables, 'dataTypes' => $dataTypes]);
    }

    public function actionDelete($id)
    {
        $response = Table::deleteTable($id);

        //echo "<pre>"; print_r($response);

        if ($response['requestresult'] == 'unsuccessfully') {
            Yii::$app->getSession()->setFlash('danger', Yii::t('app', $response['extendedinfo']));
        } else {
            Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Successfully delete'));
        }

        return $this->redirect(['index']);
    }

    public function actionGetTableColumns()
    {
        $table = Yii::$app->request->post('table_name');

        $fields = strtolower(Yii::$app->request->post('fields'));
        $fields_arr = json_decode($fields);

        $selected_source_column = Yii::$app->request->post('selected_source_column');

        $tableColumns = ExtensionsList::getTableColumns($table);

        $table_cols = array();

        foreach ($tableColumns as $key => $columns) {
            foreach ($selected_source_column as $column) {
                if (in_array($column, $fields_arr->name)) {
                    if (in_array($columns['data_type'],
                            $fields_arr->datatype) && ($columns['column_key'] != '' && $columns['column_key'] != null)) {
                        $table_cols[$key] = $columns;
                    }
                }
            }
        }

        //echo "<pre>"; print_r($table_cols);

        $response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $response->data = $table_cols;

        return $response;
    }

    public function actionUploadCsv($id)
    {
        $modelClass = $this->model;
        $modelClassForm = $this->modelForm;
        $model = new $modelClassForm();

        if ($post = Yii::$app->request->post()) {
            echo "Post Before :: <pre>"; print_r($post);

			$model->attributes = Yii::$app->request->post();

			if(!empty($_FILES["TableForm"]["tmp_name"]["csv_file"])) {
				$final_post = array();
				$columns = array();
				$i = 0;

				$file = UploadedFile::getInstance($model,'csv_file');

				$fp = fopen($file->tempName, 'r');

				if($fp) {
					//	$line = fgetcsv($fp, 1000, ",");
					//	print_r($line); exit;
					$first_time = true;
					do {
						if ($first_time == true) {
							$first_time = false;
							continue;
						}

						$columns[$i]['name'] = $line[0];
						$columns[$i]['type'] = $line[1];
						$columns[$i]['nullable'] = $line[2];
						$columns[$i]['scale'] = $line[3];
						$columns[$i]['length'] = $line[4];

						$i++;
					} while(($line = fgetcsv($fp, 1000, ",")) != FALSE);
				}

				fclose($fp);

				echo "Columns :: <pre>"; print_r($columns);

				$final_post['columns'] = $columns;
			} else {
				
			}

			echo "Post After :: <pre>"; print_r($final_post);

            /*if ($response['requestresult'] == 'unsuccessfully') {
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Successfully create'));
            } else {
                Yii::$app->getSession()->setFlash('danger', Yii::t('app', $response['extendedinfo']));
            }*/

            die;
        }
    }

    public function actionDownloadCsv($id)
    {
        $modelClass = $this->model;
        $modelClassForm = $this->modelForm;

        $model = $modelClass::GetTableInfo($id);

        //echo "<pre>"; print_r($model);

        $fileName = $id;

        ob_clean();
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=' . $fileName . '.csv');

        // Start the ouput
        $output = fopen("php://output", "w");

        $final_column = array();

        // Then loop through the rows
        foreach($model->columns as $key => $column) {
            // Add the rows to the body
            if($column['name'] != 'LockedBy' && $column['name'] != 'LockTime') {
                $final_array[] = $column['name'];
            }
        }

        fputcsv($output, $final_array); // here you can change delimiter/enclosure

        // Close the stream off
        fclose($output);

        ob_flush();

        exit();
    }

}