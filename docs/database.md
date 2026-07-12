# Database Rules

Use migration.
Use foreign keys.
Use Eloquent Relationships.

Prefer:
hasMany
belongsTo
belongsToMany

Avoid:
Raw SQL
SELECT \*

Prevent:
N+1 Query

Use:
with()
load()
paginate()

Never fetch unnecessary columns.
