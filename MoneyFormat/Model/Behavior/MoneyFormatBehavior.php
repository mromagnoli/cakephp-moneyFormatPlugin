<?php
App::uses('ModelBehavior', 'Model');

/**
 * MoneyFormat behavior
 *
 * Change currency format from US to Europe form.
 *
 */
class MoneyFormatBehavior extends ModelBehavior {


	public $settings = array();

/**
 * Set configs for behavior well functioning.
 *
 * @param Model $model Model using this behavior
 * @param array $config Configuration settings for $model
 * @return void
 */
	public function setup(Model $model, $config = array()) {
		if (empty($config['fields'])) {
			trigger_error(__('\'fields\' property for MoneyFormatBehavior must be set.'), E_USER_WARNING);
			return false;
		}

		$this->settings = array_merge($this->settings, $config);
	}

/**
 * Add a correspondent validation rule.
 *
 * @param Model $model Model using this behavior
 * @param array $options Options passed from Model::save().
 * @return mixed False or null will abort the operation. Any other result will continue.
 * @see Model::save()
 */
	public function beforeValidate(Model $model, $options = array()) {
		$fields = $this->settings['fields'];
		foreach ($fields as $field) {
			$model->validate[$field]['money_format'] = [
				'rule' => '/(^\d{1,3}(\.?\d{3})*(,\d+)?$)/',
				'message' => 'invalid value',
				'allowEmpty' => false
			];
			if (!empty($this->settings['allowEmpty'][$field])) {
				$model->validate[$field]['money_format']['allowEmpty'] = $this->settings['allowEmpty'][$field];
			}
		}
	}

/**
 * Format values.
 *
 * @param Model $model Model using this behavior
 * @param array $options Options passed from Model::save().
 * @return mixed False if the operation should abort. Any other result will continue.
 * @see Model::save()
 */
	public function beforeSave(Model $model, $options = array()) {
		$fields = $this->settings['fields'];
		foreach ($fields as $field) {
			if (!empty($model->data[$model->alias][$field])) {
				$value =& $model->data[$model->alias][$field];
				$value = str_replace('.', '', $value);
				$value = str_replace(',', '.', $value);
			}
		}
	}

/**
 * Format fields in order to return as were saved.
 *
 * @param Model $model Model using this behavior
 * @param mixed $results The results of the find operation
 * @param bool $primary Whether this model is being queried directly (vs. being queried as an association)
 * @return mixed An array value will replace the value of $results - any other value will be ignored.
 */
	public function afterFind(Model $model, $results, $primary = false) {
		if (empty($results)) {
			return;
		}

		$fields = $this->settings['fields'];
		foreach ($results as $k => $v) {
			$data = Hash::extract($v, $model->alias);
			foreach ($fields as $field) {
				if (!empty($data[$field])) {
					$results[$k][$model->alias][$field] = str_replace('.', ',', $data[$field]);
				}
			}
		}

		return $results;
	}
}