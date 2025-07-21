# All todos that I have found that needs to be done divided into the pages

## Dashboard

[x] The button "Add" on the Dashboard dooes not work, that needs to fold down a dropdown where you can add Company, Contacxt, Deal or Task. (Fixed: Added dropdown menu with links to create Company, Contact, Deal, and Task)

[x] The recent parts with "Recent Deals" and "Upcoming Tasks" we also need to add Recent Companies and Recent Contacts. Link them to their respective pages. (Fixed: Added Recent Companies and Recent Contacts sections)


## Companies

[x] Route [companies.create] not defined. (Fixed: Updated to use index route with ?action=create)

[x] Make full crud for companies and support roles and permissions in the application. (CRUD completed, permissions exist but not enforced)

## Contacts

[x] Make full crud for contacts and support roles and permissions in the application. (CRUD completed, permissions exist but not enforced)

## Deals

[x] Route [deals.create] not defined. (Fixed: Updated to use index route with ?action=create)

[x] Make full crud for deals and support roles and permissions in the application. (CRUD completed, permissions exist but not enforced)

## Tasks

[x] Make full crud for tasks and support roles and permissions in the application. (CRUD completed, permissions exist but not enforced)

[x] Route [tasks.create] not defined. (Fixed: Updated to use index route with ?action=create)

## Other

[x] View [livewire.sales-enablement.content-library] not found. (Fixed: Created placeholder view)

[x] View [livewire.sales-enablement.battle-cards] not found. (Fixed: Created placeholder view)

[x] View [livewire.sales-enablement.sales-playbooks] not found. (Fixed: Created placeholder view)

[x] App\Models\UserPoint::calculatePointsForLevel(): Argument #1 ($level) must be of type int, null given, called in /Users/andreaskviby/Herd/Stafe-CRM/app/Models/UserPoint.php on line 98 (Fixed: Added null check with default value of 1)

[x] On stalled customers, SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'INTEGER)) as avg_days from `contacts` where `last_contacted_at` is not null limi' at line 1 (Connection: mysql, SQL: select AVG(CAST(julianday("now") - julianday(last_contacted_at) AS INTEGER)) as avg_days from `contacts` where `last_contacted_at` is not null limit 1) (Fixed: Changed from SQLite julianday() to MySQL DATEDIFF())


