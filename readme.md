### Custom validator for Laravel

Wrapper to validate any data array according to reusable sets of rules. 
Has a few yii2-style wrappers for getting validation results.

### Usage example

Define your set of rules, for example, using a Model

```
class ModelSet implements \AirBear\Validation\Rules\RulesSet
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return [
            'title'         => ['required', 'max:255', Rule::unique($this->model->getTable())->ignoreModel($this->model)],
            'description'   => ['required'],
        ];
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
        return [
        ];
    }
}

```

Use it where needed

```
$validator = new Validator();
$validator->validate($request->all(), new ModelSet($someModel));
$isTitleCorrect = $validator->hasErrors('title');
```
