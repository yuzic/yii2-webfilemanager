<?php
print_r($model);
$response = ['model' => $model];
echo json_encode($response, true);
