# Formation Module - Quick Reference Guide

## File Directory Structure

```
app/
├── Enums/
│   ├── Formation/
│   │   └── FormationStatus.php
│   ├── Qualification/
│   │   └── QualificationStatus.php
│   ├── Diplome/
│   │   └── DiplomeStatus.php
│   └── Licence/
│       └── LicenceStatus.php
│
├── Models/
│   └── Formation/
│       ├── Niveau.php
│       ├── Categorie.php
│       ├── SousCategorie.php
│       ├── Instructeur.php
│       ├── Examinateur.php
│       ├── Formation.php
│       ├── Classe.php
│       ├── Participant.php
│       ├── Qualification.php
│       ├── Diplome.php
│       ├── Licence.php
│       ├── Justificatif.php
│       └── SituationSCE.php
│
├── Http/
│   ├── Requests/
│   │   └── Formation/
│   │       ├── StoreFormationRequest.php
│   │       ├── UpdateFormationRequest.php
│   │       ├── StoreParticipantRequest.php
│   │       ├── UpdateParticipantRequest.php
│   │       ├── StoreQualificationRequest.php
│   │       ├── StoreDiplomeRequest.php
│   │       ├── StoreLicenceRequest.php
│   │       ├── StoreClasseRequest.php
│   │       └── StoreJustificatifRequest.php
│   │
│   └── Controllers/
│       └── Formation/
│           ├── FormationController.php
│           ├── ParticipantController.php
│           ├── QualificationController.php
│           ├── DiplomeController.php
│           ├── LicenceController.php
│           ├── ClasseController.php
│           ├── JustificatifController.php
│           └── ReportController.php
│
├── Services/
│   └── Formation/
│       ├── FormationService.php
│       ├── ParticipantService.php
│       ├── QualificationService.php
│       ├── DiplomeService.php
│       ├── LicenceService.php
│       ├── ClasseService.php
│       ├── JustificatifService.php
│       └── ReportService.php
│
└── Policies/
    ├── FormationPolicy.php
    ├── ParticipantPolicy.php
    ├── QualificationPolicy.php
    ├── DiplomePolicy.php
    ├── LicencePolicy.php
    ├── ClassePolicy.php
    └── JustificatifPolicy.php

database/
├── migrations/
│   ├── 2026_02_15_040000_create_niveaux_table.php
│   ├── 2026_02_15_040100_create_categories_table.php
│   ├── 2026_02_15_040200_create_sous_categories_table.php
│   ├── 2026_02_15_040300_create_instructeurs_table.php
│   ├── 2026_02_15_040400_create_examinateurs_table.php
│   ├── 2026_02_15_040500_create_formations_table.php
│   ├── 2026_02_15_040600_create_formation_instructeur_table.php
│   ├── 2026_02_15_040700_create_formation_examinateur_table.php
│   ├── 2026_02_15_040800_create_classes_table.php
│   ├── 2026_02_15_040900_create_participants_table.php
│   ├── 2026_02_15_041000_create_qualifications_table.php
│   ├── 2026_02_15_041100_create_diplomes_table.php
│   ├── 2026_02_15_041200_create_licences_table.php
│   ├── 2026_02_15_041300_create_justificatifs_table.php
│   └── 2026_02_15_041400_create_situation_sces_table.php
│
└── factories/
    ├── NiveauFactory.php
    ├── CategorieFactory.php
    ├── SousCategorieFactory.php
    ├── FormationFactory.php
    ├── InstructeurFactory.php
    ├── ExaminateurFactory.php
    ├── ClasseFactory.php
    ├── ParticipantFactory.php
    ├── QualificationFactory.php
    ├── DiplomeFactory.php
    ├── LicenceFactory.php
    ├── JustificatifFactory.php
    └── SituationSCEFactory.php

tests/
├── Feature/
│   └── Formation/
│       ├── FormationControllerTest.php
│       └── ParticipantControllerTest.php
│
└── Unit/
    └── Formation/
        ├── FormationServiceTest.php
        ├── QualificationServiceTest.php
        ├── DiplomeServiceTest.php
        ├── LicenceServiceTest.php
        └── ClasseServiceTest.php
```

---

## Common Operations

### Creating a Formation

```php
use App\Services\Formation\FormationService;

$formationService = new FormationService();

$formation = $formationService->createFormation([
    'code' => 'FORM-001',
    'nom' => 'Formation Title',
    'niveau_id' => 1,
    'categorie_id' => 1,
    'sous_categorie_id' => 1,
    'date_debut' => '2026-03-01',
    'date_fin' => '2026-04-30',
    'lieu' => 'Training Center',
    'duree_heures' => 40,
    'nombre_participants' => 25,
]);
// auto-sets status to 'planifiée'
```

### Adding Participants

```php
use App\Services\Formation\ParticipantService;

$participantService = new ParticipantService();

$participant = $participantService->createParticipant($formation, [
    'prenom' => 'Jean',
    'nom' => 'Dupont',
    'email' => 'jean@example.com',
    'numero_identite' => 'A123456789',
    'date_debut' => '2026-03-01',
    'date_fin' => '2026-04-30',
]);
```

### Recording Qualifications

```php
use App\Services\Formation\QualificationService;

$qualificationService = new QualificationService();

// Record evaluation
$qualification = $qualificationService->recordQualification([
    'formation_id' => 1,
    'participant_id' => 1,
    'score' => 85,
    'date_evaluation' => '2026-04-30',
    'evaluateur_id' => 5,
]);
// auto-sets status to 'en attente'

// Approve
$qualificationService->approveQualification($qualification);

// Or reject
$qualificationService->rejectQualification($qualification, 'Score insufficient');
```

### Creating & Delivering Diplomas

```php
use App\Services\Formation\DiplomeService;

$diplomeService = new DiplomeService();

// Create
$diplome = $diplomeService->createDiplome([
    'formation_id' => 1,
    'participant_id' => 1,
    'numero_diplome' => 'DIPL-001-2026',
    'date_emission' => '2026-05-01',
]);
// auto-sets status to 'à livrer'

// Send for examination
$diplomeService->sendForExamination($diplome);

// Deliver
$diplomeService->deliverDiplome($diplome);
// or reject
$diplomeService->rejectDiplome($diplome, 'Documents missing');
```

### Managing Licenses

```php
use App\Services\Formation\LicenceService;

$licenceService = new LicenceService();

// Create
$licence = $licenceService->createLicence([
    'formation_id' => 1,
    'participant_id' => 1,
    'numero_licence' => 'LIC-001-2026',
    'date_emission' => '2026-05-01',
    'date_expiration' => '2027-05-01',
]);

// Deliver
$licenceService->deliverLicence($licence);

// Renew
$licenceService->renewLicence($licence, [
    'date_expiration' => '2028-05-01',
]);

// Suspend
$licenceService->suspendLicence($licence, 'Non-compliance');
```

### Checking Capacity

```php
use App\Services\Formation\ClasseService;

$classeService = new ClasseService();

$classe = Classe::find(1);

// Check if we can add more
if ($classeService->canEnrollParticipant($classe)) {
    // assign participant
}

// Get available spots
$available = $classeService->getRemainingCapacity($classe);

// Get enrolled count
$enrolled = $classeService->getEnrolledCount($classe);
```

### Getting Statistics

```php
use App\Services\Formation\ReportService;

$reportService = new ReportService();

// Full formation report
$report = $reportService->generateFormationReport($formation);
// Returns: total_participants, taux_qualification, taux_diplomation, etc.

// Overall stats
$formationStats = $reportService->getFormationStatistics();
$qualificationStats = $reportService->getQualificationStatistics();
$diplomationStats = $reportService->getDiplomationStatistics();
$licenceStats = $reportService->getLicenceStatistics();
```

---

## Key Relationships

### Formation Relationships
```php
$formation->niveau;           // BelongsTo Niveau
$formation->categorie;        // BelongsTo Categorie
$formation->sousCategorie;    // BelongsTo SousCategorie
$formation->classes;          // HasMany Classe
$formation->participants;     // HasMany Participant
$formation->qualifications;   // HasMany Qualification
$formation->diplomes;         // HasMany Diplome
$formation->licences;         // HasMany Licence
$formation->justificatifs;    // HasMany Justificatif
$formation->instructeurs;     // BelongsToMany with pivot
$formation->examinateurs;     // BelongsToMany with pivot
```

### Participant Relationships
```php
$participant->formation;      // BelongsTo Formation
$participant->classe;         // BelongsTo Classe
$participant->qualification;  // HasOne Qualification
$participant->diplome;        // HasOne Diplome
$participant->licence;        // HasOne Licence
```

---

## Query Examples

### Find all qualified participants
```php
$qualified = Participant::whereHas('qualification', function ($query) {
    $query->where('status', 'qualifié');
})->get();
```

### Find formations with low qualification rates
```php
$formations = Formation::with('qualifications')
    ->get()
    ->filter(function ($formation) {
        return $formation->qualifications
            ->where('status', 'qualifié')
            ->count() / $formation->participants->count() * 100 < 50;
    });
```

### Get all delivered diplomas
```php
$deliveredDiplomas = Diplome::where('status', 'livrée')
    ->with(['participant', 'formation'])
    ->get();
```

### Find expiring licenses (next 30 days)
```php
$expiringLicences = Licence::whereBetween('date_expiration', [
    now(),
    now()->addDays(30),
])->where('status', '!=', 'expirée')->get();
```

---

## Validation Cheat Sheet

### Formation Input
```php
[
    'code' => 'FORM-001',              // unique
    'nom' => 'Formation Name',         // required
    'niveau_id' => 1,                  // must exist
    'categorie_id' => 1,               // must exist
    'date_debut' => '2026-03-01',      // before date_fin
    'date_fin' => '2026-04-30',        // after date_debut
]
```

### Participant Input
```php
[
    'prenom' => 'Jean',                // required
    'nom' => 'Dupont',                 // required
    'email' => 'jean@example.com',     // unique
    'numero_identite' => 'ID123',      // unique
    'date_debut' => '2026-03-01',
    'date_fin' => '2026-04-30',        // after date_debut
]
```

### Qualification Input
```php
[
    'formation_id' => 1,               // must exist
    'participant_id' => 1,             // must exist
    'status' => 'en attente',          // in enum
    'score' => 85,                     // min 0, max 100
]
```

---

## Authorization Quick Reference

| Action | Role Required | Method |
|--------|---|---|
| View any formation | admin/super_admin | `FormationPolicy@viewAny` |
| Create formation | admin/super_admin | `FormationPolicy@create` |
| Update formation | admin/super_admin | `FormationPolicy@update` |
| Delete formation | super_admin | `FormationPolicy@delete` |
| Execute formation | admin/super_admin | `FormationPolicy@execute` |
| Archive formation | super_admin | `FormationPolicy@archive` |

---

## Testing Quick Commands

```bash
# Run all formation tests
php artisan test tests/Feature/Formation/ --compact

# Run specific test
php artisan test tests/Feature/Formation/FormationControllerTest.php --compact

# Run with filter
php artisan test --filter=testFormationCreate --compact

# Run unit tests only
php artisan test tests/Unit/ --compact

# With coverage
php artisan test --coverage
```

---

## Common Issues & Solutions

| Problem | Solution |
|---------|----------|
| "Target [FormationService] is not instantiable" | Inject via constructor: `public function __construct(FormationService $service)` |
| "Route not found" | Run `php artisan route:list` to verify routes, check route names |
| "Policy exception" | Check user role: `auth()->user()->role->value` should be 'admin' or 'super_admin' |
| "Unique constraint violates" | Check for existing record: `Formation::where('code', $code)->exists()` |
| "Foreign key constraint fails" | Ensure parent record exists before creating child |

---

## Performance Tips

- Always use `with()` for eager loading relationships
- Use pagination on list views: `->paginate(15)`
- Index frequently queried columns (status, dates)
- cache([' formation reports after generating
- Consider materialized view for SituationSCE stats

---

## Useful Artisan Commands

```bash
# Clear all caches
php artisan cache:clear

# View all routes
php artisan route:list

# List all migration statuses
php artisan migrate:status

# Roll back last migration batch
php artisan migrate:rollback

# Generate query log
php artisan tinker
>>> DB::listen(function($query) { dump($query); });
```

---

Version: 1.0.0  
Last Updated: 2026-02-15
