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
- [ ] add timestamp
- [ ] add enum https://laravel.com/docs/11.x/eloquent-mutators#enum-casting
- 
### CreateModel
- [ ] Resolve namespace automatically creating folders
- [ ] Set path/Configure root folder for all models create
- [ ] Handle exception: Model already exists

### UpdateModel
- [ ] Change primary key

### RemoveModel
- [ ] Make it work. Check dependency in the code

### RenameModel
- [ ] Create a version with the older name e.g. NewName extends OldName

