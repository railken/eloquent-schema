- [x] Add a new attribute
- [x] Remove an attribute
- [x] Manage migrations
- [x] Rename attribute
- [x] Update attribute
- [x] When "renaming" an attribute, an update occurs in all eloquent module. Not only database but also eloquent model has to be retrieved in order to fill fields such as: validation, fillable, etc... 
- [ ] Tests updates and check all differences between fillable/required/validation
- [x] Added hooks to Eloquent\Model
- [x] Add hooks so dev can add custom rules when something is added, eg. extra param
- [ ] Migrate migration stubs to injector (even text maybe -> -> ->)
- [ ] Create new model
- [ ] Rename model
- [ ] Delete model
- [ ] Manage indexes
- [ ] Manage relationships
- [ ] Manage timestamps
- [ ] Manage soft delete
- [ ] Test with different attributes
- [ ] Separate tests based on action
- [ ] Find a way for better text then new file
- [ ] Add hook for custom file expect models/migration (e.g. schema or validator)
- [ ] phpstan level max
- [x] fix errors in injectors about variable not existant

- [ ] Migrations without changes to the db should not be created
- [ ] Update hooks with "change" set/unset based on current attribute and remove from other places
- [ ] add default value

Attributes to add

- [ ] Custom "casts" by class https://laravel.com/docs/11.x/eloquent-mutators#custom-casts
- [ ] add boolean
- [ ] add date https://laravel.com/docs/11.x/eloquent-mutators#date-casting
- [ ] add datetime
- [ ] add array/json https://laravel.com/docs/11.x/eloquent-mutators#array-and-json-casting
- [ ] add number that will manage double, float, integer, decimal and others
- [ ] add timestamp
- [ ] add enum https://laravel.com/docs/11.x/eloquent-mutators#enum-casting