# CakePHP 2.x MoneyFormat Plugin
##### Converts US money format to EU money format before and after reading and writing from/to DB.

This is a Plugin for CakePHP v2.x that implements a behavior in order to read and write Europe money format, i.e. point to separate thousands  and comma in order to set decimal part.

Even this Plugin it's intended for money fields, you can use it with any other numeric fields where you want to have this feature.

##Example
If you have a field in model called `price` and it has value `12.05`, when you retrieve this value you are going to see it as `12,05`.

In the other hand, if you want to set values to money fields (or whatever numeric field you want to use), you can set it with commas as in `130,99`. In this case, the Plugin gonna save it as `130.99`. Also, you can separate thousands with point as in `12.000,35` and it will be save as `12000.35`.

## Installation
* Clone repo into the app/Plugin directory.
```sh
$ git clone https://github.com/mromagnoli/cakephp-moneyFormatPlugin.git MoneyFormat
```
* Load Plugin in app/Config/bootstrap.php

    ![load](https://cloud.githubusercontent.com/assets/1746271/7442128/59a76056-f0da-11e4-896f-a45faf112e6c.png)

* Attach to desired Model
  ![attach](https://cloud.githubusercontent.com/assets/1746271/7442150/192af7bc-f0db-11e4-8b29-0029937a6459.png)
 * **Options**
    * `fields` Array of strings with model fields to take in account. 
    * `allowEmpty` Array of fields as key and boolean as value, in order to allow empty fields, if other validation rules did not set for that field.

* Just read and write from/to DB as usual. The behavior does all the job.
###Note
Cake ***does not*** attach behaviors, then it is not applied, if the model where was attached is retrieved as related or contained model of another one.
So you have to call manually behavior's afterFind method. In your model, add this to your afterFind method:
```php
public function afterFind($results, $primary = false) {
		if (!$MoneyFormat = $this->Behaviors->MoneyFormat) {
			$MoneyFormat = $this->Behaviors->load(
				'MoneyFormat.MoneyFormat',
				[
					'fields' => ['price', 'total_price', 'deposit', 'due'] // Set your own fields where to apply
				]
			);
		}
		return $MoneyFormat->afterFind($this, $results, ['fields' => $MoneyFormat->settings['fields']]);
	}
```

