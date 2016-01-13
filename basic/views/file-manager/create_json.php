<?php
$response = ['model' => $model->jsonSerialize()];
if ($model->hasErrors()) {
	$response['errors'] = $model->getErrors();
}
echo json_encode($response);