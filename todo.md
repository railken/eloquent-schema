- [x] Add a new attribute
- [x] Remove an attribute
- [x] Manage migrations
- [x] Rename attribute
- [x] Update attribute
- [x] When "renaming" an attribute, an update occurs in all eloquent module. Not only database but also eloquent model has to be retrieved in order to fill fields such as: validation, fillable, etc... 
- [x] Added hooks to Eloquent\Model
- [x] Add hooks so dev can add custom rules when something is added, eg. extra param
- [ ] Migrate migration stubs to injector (even text maybe -> -> ->)
- [ ] Manage indexes
- [ ] Manage relationships
- [ ] Manage timestamps
- [x] fix errors in injectors about variable not existant
- [ ] Migrations without changes to the db should not be created
- [ ] Update hooks with "change" set/unset based on current attribute and remove from other places
- [x] add default value
- [x] refactor tests
- [x] Separate tests based on action
- [x] Find a way for better text then new file
- [ ] Add hook for custom file expect models/migration (e.g. schema or validator)
- [ ] phpstan level max
- [x] Create new model
- [ ] Rename model
- [ ] Delete model
- [ ] Tests updates and check all differences between fillable/required/validation
- [ ] Some eloquent models might not need $fillable, some might, add customization
- [x] Add migration/model as customizable


### DeleteAtAttribute
- [ ] New attribute to add. Add Trait as well SoftDelete to model

### Attributes
- [ ] Errors: Name can only be alphanumeric
- [ ] More tests between up/down of nullable, required, fillable, default
- [ ] Manage order for placements attributes: E.g. $guarded should be placed after tables?
- [ ] Test with different attributes
- [ ] Custom "casts" by class https://laravel.com/docs/11.x/eloquent-mutators#custom-casts
- [ ] add boolean
- [ ] add date https://laravel.com/docs/11.x/eloquent-mutators#date-casting
- [ ] add datetime
- [ ] add array/json https://laravel.com/docs/11.x/eloquent-mutators#array-and-json-casting
- [ ] add number that will manage double, float, integer, decimal and others
- [x] add timestamp
- [ ] add enum https://laravel.com/docs/11.x/eloquent-mutators#enum-casting


### CreateModel
- [x] Resolve namespace automatically creating folders
- [x] Set path/Configure root folder for all models create
- [x] Handle exception: Model already exists
- [ ] Separate timestamps
- [ ] Separate ids

### UpdateModel
- [ ] Change primary key

### RemoveModel
- [x] Add RemoveModel
- [ ] Check dependency in the code
- [ ] renderUp should re-add all the columns

### RenameModel
- [ ] Create a version with the older name e.g. NewName extends OldName

-----
- [ ] Sort methods/attributes. Lint. Rules: https://cs.symfony.com/doc/rules/attribute_notation/ordered_attributes.html
- [ ] Generate records of all models from code
- [ ] Get attributes from fillable/guarded, etc for Eloquent (maybe read migration code), get them from schema for Amethyst
- [ ] Add new record with source: user instead of code
- [ ] Automatic generation based on difference
- [ ] Handle relationships


--------
Rename Hook: MutableProperty or something
Refactor ModelActionCreate/Update too bad
Manage changes with model attributes instead of create/update