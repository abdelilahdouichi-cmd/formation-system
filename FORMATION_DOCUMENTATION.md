# Formation / Qualification / Licences Module - Developer Documentation

**Scope**
This document is derived strictly from code in this repository. It covers the Formation, Qualification, Diplôme, Licence, and related list concepts (niveaux, catégories, sous-catégories, classes, participants, instructeurs, examinateurs, justificatifs, situation SCE).

**Glossary**
| Concept | Meaning in this codebase | Model / Table |
| --- | --- | --- |
| Formation | Session de formation avec statut, dates, lieu, niveau/catégorie et participants. | `app/Models/Formation/Formation.php` / `formations` |
| Qualification | Évaluation d’un participant sur une formation. | `app/Models/Formation/Qualification.php` / `qualifications` |
| Diplôme | Certificat délivré à un participant d’une formation. | `app/Models/Formation/Diplome.php` / `diplomes` |
| Licence | Licence associée à un participant d’une formation. | `app/Models/Formation/Licence.php` / `licences` |
| SCE | Situation SCE, agrégats et statistiques par formation. | `app/Models/Formation/SituationSCE.php` / `situation_sces` |
| Classe | Groupe de formation rattaché à une formation. | `app/Models/Formation/Classe.php` / `classes` |
| Niveau | Niveau de formation, parent des catégories. | `app/Models/Formation/Niveau.php` / `niveaux` |
| Catégorie | Catégorie rattachée à un niveau. | `app/Models/Formation/Categorie.php` / `categories` |
| Sous-catégorie | Sous-catégorie rattachée à une catégorie. | `app/Models/Formation/SousCategorie.php` / `sous_categories` |
| Participant | Personne inscrite à une formation. | `app/Models/Formation/Participant.php` / `participants` |
| Instructeur | Formateur, assigné aux formations via pivot. | `app/Models/Formation/Instructeur.php` / `instructeurs` |
| Examinateur | Examinateur, assigné aux formations via pivot. | `app/Models/Formation/Examinateur.php` / `examinateurs` |
| Justificatif | Document lié à une formation, participant, diplôme ou licence. | `app/Models/Formation/Justificatif.php` / `justificatifs` |

**Data Model**
```text
Niveau 1─* Categorie 1─* SousCategorie
Niveau 1─* Formation
Categorie 1─* Formation
SousCategorie 1─* Formation

Formation 1─* Classe
Formation 1─* Participant
Formation 1─* Qualification
Formation 1─* Diplome
Formation 1─* Licence
Formation 1─* Justificatif
Formation 1─* SituationSCE

Formation *─* Instructeur (formation_instructeur)
Formation *─* Examinateur (formation_examinateur)

Classe 1─* Participant
Participant 1─0..1 Qualification
Participant 1─0..1 Diplome
Participant 1─0..1 Licence
```

| Table | Key fields (non‑exhaustive) | Relations |
| --- | --- | --- |
| `niveaux` | `nom`, `ordre`, `is_active` | hasMany `categories`, hasMany `formations` |
| `categories` | `niveau_id`, `code`, `is_active` | belongsTo `niveaux`, hasMany `sous_categories`, hasMany `formations` |
| `sous_categories` | `categorie_id`, `code`, `is_active` | belongsTo `categories`, hasMany `formations` |
| `instructeurs` | `nom`, `prenom`, `email`, `numero_licence`, `date_debut`, `date_fin` | belongsToMany `formations` |
| `examinateurs` | `nom`, `prenom`, `email`, `numero_licence`, `organisme`, `date_debut`, `date_fin` | belongsToMany `formations` |
| `formations` | `code`, `nom`, `status`, `date_debut_prevue`, `date_fin_prevue`, `date_debut_reelle`, `date_fin_reelle`, `niveau_id`, `categorie_id`, `sous_categorie_id`, `nombreParticipantsMin`, `nombreParticipantsMax`, `is_active` | belongsTo `niveaux`, `categories`, `sous_categories`; hasMany `classes`, `participants`, `qualifications`, `diplomes`, `licences`, `justificatifs`, `situation_sces`; belongsToMany `instructeurs`, `examinateurs` |
| `formation_instructeur` | `formation_id`, `instructeur_id`, `role`, `date_debut`, `date_fin` | pivot |
| `formation_examinateur` | `formation_id`, `examinateur_id`, `date_examen`, `lieu` | pivot |
| `classes` | `formation_id`, `code`, `capacite`, `capacite_maximal`, `date_debut`, `date_fin`, `is_active` | belongsTo `formations`, hasMany `participants` |
| `participants` | `formation_id`, `classe_id`, `email`, `numero_identite`, `date_inscription`, `date_debut`, `date_fin`, `is_active` | belongsTo `formations`, belongsTo `classes`, hasOne `qualification`, `diplome`, `licence` |
| `qualifications` | `formation_id`, `participant_id`, `status`, `score`, `date_evaluation`, `evaluateur_id` | belongsTo `formations`, `participants`, `instructeurs` |
| `diplomes` | `formation_id`, `participant_id`, `numero_diplome`, `status`, `date_delivrance`, `date_expiration`, `signature_directeur` | belongsTo `formations`, `participants` |
| `licences` | `formation_id`, `participant_id`, `numero_licence`, `status`, `date_delivrance`, `date_expiration`, `date_renouvellement` | belongsTo `formations`, `participants` |
| `justificatifs` | `formation_id`, `participant_id`, `diplome_id`, `licence_id`, `type`, `nom_fichier`, `chemin_fichier`, `taille`, `mime_type`, `date_upload`, `is_verified`, `verified_by` | belongsTo `formations`, `participants`, `diplomes`, `licences`, `instructeurs` |
| `situation_sces` | `formation_id`, `sce_name`, `nombre_inscrits`, `nombre_presents`, `nombre_absents`, `nombre_qualifies`, `nombre_non_qualifies`, `nombre_diplomes`, `nombre_licences`, `statut_rapport`, `date_rapport` | belongsTo `formations` |
| `audit_logs` | `user_id`, `method`, `path`, `action`, `model_type`, `model_id` | belongsTo `users` |

**Routes**
All routes below are web routes in `routes/web.php` under prefix `admin` with middleware `auth`, `role:admin,super_admin`, `log.admin`.

**Formations**
| Method | Path | Action | Request params | Response |
| --- | --- | --- | --- | --- |
| GET | `/admin/formations` | `FormationController@index` | none | Blade view `admin.formations.index` |
| GET | `/admin/formations/create` | `FormationController@create` | none | Blade view `admin.formations.create` |
| POST | `/admin/formations` | `FormationController@store` | `niveau_id`, `categorie_id`, `sous_categorie_id`, `nom`, `code`, `description`, `objectif`, `duree_heures`, `prix`, `date_debut_prevue`, `date_fin_prevue`, `lieu`, `nombreParticipantsMin`, `nombreParticipantsMax`, `is_active` | Redirect to `admin.formations.show` |
| GET | `/admin/formations/{formation}` | `FormationController@show` | route param `formation` | Blade view `admin.formations.show` |
| GET | `/admin/formations/{formation}/edit` | `FormationController@edit` | route param `formation` | Blade view `admin.formations.edit` |
| PUT/PATCH | `/admin/formations/{formation}` | `FormationController@update` | `niveau_id`, `categorie_id`, `sous_categorie_id`, `nom`, `code`, `description`, `objectif`, `duree_heures`, `prix`, `date_debut_prevue`, `date_fin_prevue`, `date_debut_reelle`, `date_fin_reelle`, `status`, `lieu`, `nombreParticipantsMin`, `nombreParticipantsMax`, `is_active` | Redirect to `admin.formations.show` |
| DELETE | `/admin/formations/{formation}` | `FormationController@destroy` | route param `formation` | Redirect to `admin.formations.index` |
| POST | `/admin/formations/{formation}/execute` | `FormationController@execute` | route param `formation` | Redirect back |
| POST | `/admin/formations/{formation}/cancel` | `FormationController@cancel` | route param `formation` | Redirect back |
| POST | `/admin/formations/{formation}/archive` | `FormationController@archive` | route param `formation` | Redirect back |

**Classes**
| Method | Path | Action | Request params | Response |
| --- | --- | --- | --- | --- |
| GET | `/admin/formations/{formation}/classes` | `ClasseController@index` | route param `formation` | Blade view `admin.classes.index` |
| GET | `/admin/formations/{formation}/classes/create` | `ClasseController@create` | route param `formation` | Blade view `admin.classes.create` |
| POST | `/admin/formations/{formation}/classes` | `ClasseController@store` | `formation_id`, `code`, `nom`, `capacite_maximal`, `date_debut`, `date_fin`, `lieu`, `description` | Redirect to `admin.formations.classes.show` |
| GET | `/admin/formations/{formation}/classes/{class}` | `ClasseController@show` | route params `formation`, `class` | Blade view `admin.classes.show` |
| GET | `/admin/formations/{formation}/classes/{class}/edit` | `ClasseController@edit` | route params `formation`, `class` | Blade view `admin.classes.edit` |
| PUT/PATCH | `/admin/formations/{formation}/classes/{class}` | `ClasseController@update` | `formation_id`, `code`, `nom`, `capacite_maximal`, `date_debut`, `date_fin`, `lieu`, `description` | Redirect to `admin.formations.classes.show` |
| DELETE | `/admin/formations/{formation}/classes/{class}` | `ClasseController@destroy` | route params `formation`, `class` | Redirect to `admin.formations.classes.index` |

**Participants**
| Method | Path | Action | Request params | Response |
| --- | --- | --- | --- | --- |
| GET | `/admin/formations/{formation}/participants` | `ParticipantController@index` | route param `formation` | Blade view `admin.participants.index` |
| GET | `/admin/formations/{formation}/participants/create` | `ParticipantController@create` | route param `formation` | Blade view `admin.participants.create` |
| POST | `/admin/formations/{formation}/participants` | `ParticipantController@store` | `formation_id`, `classe_id`, `nom`, `prenom`, `email`, `telephone`, `entreprise`, `poste`, `numero_identite`, `date_inscription`, `date_debut`, `date_fin`, `is_active` | Redirect to `admin.participants.show` |
| GET | `/admin/formations/{formation}/participants/{participant}` | `ParticipantController@show` | route params `formation`, `participant` | Blade view `admin.participants.show` |
| GET | `/admin/formations/{formation}/participants/{participant}/edit` | `ParticipantController@edit` | route params `formation`, `participant` | Blade view `admin.participants.edit` |
| PUT/PATCH | `/admin/formations/{formation}/participants/{participant}` | `ParticipantController@update` | `classe_id`, `nom`, `prenom`, `email`, `telephone`, `entreprise`, `poste`, `numero_identite`, `date_inscription`, `date_debut`, `date_fin`, `is_active` | Redirect to `admin.participants.show` |
| DELETE | `/admin/formations/{formation}/participants/{participant}` | `ParticipantController@destroy` | route params `formation`, `participant` | Redirect to `admin.participants.index` |
| POST | `/admin/formations/{formation}/participants/{participant}/assign-classe` | `ParticipantController@assignToClasse` | `classe_id` | Redirect back |
| POST | `/admin/formations/{formation}/participants/{participant}/deactivate` | `ParticipantController@deactivate` | route params `formation`, `participant` | Redirect back |
| POST | `/admin/formations/{formation}/participants/{participant}/activate` | `ParticipantController@activate` | route params `formation`, `participant` | Redirect back |

**Qualifications**
| Method | Path | Action | Request params | Response |
| --- | --- | --- | --- | --- |
| GET | `/admin/formations/{formation}/qualifications` | `QualificationController@index` | route param `formation` | Blade view `admin.qualifications.index` |
| GET | `/admin/formations/{formation}/qualifications/create` | `QualificationController@create` | route param `formation` | Blade view `admin.qualifications.create` |
| POST | `/admin/formations/{formation}/qualifications` | `QualificationController@store` | `formation_id`, `participant_id`, `status`, `score`, `date_evaluation`, `observation`, `evaluateur_id` | Redirect to `admin.formations.qualifications.show` |
| GET | `/admin/formations/{formation}/qualifications/{qualification}` | `QualificationController@show` | route params `formation`, `qualification` | Blade view `admin.qualifications.show` |
| GET | `/admin/formations/{formation}/qualifications/{qualification}/edit` | not implemented in controller | n/a | n/a |
| PUT/PATCH | `/admin/formations/{formation}/qualifications/{qualification}` | not implemented in controller | n/a | n/a |
| DELETE | `/admin/formations/{formation}/qualifications/{qualification}` | `QualificationController@destroy` | route params `formation`, `qualification` | Redirect to `admin.formations.qualifications.index` |
| POST | `/admin/formations/{formation}/qualifications/{qualification}/approve` | `QualificationController@approve` | route params `formation`, `qualification` | Redirect back |
| POST | `/admin/formations/{formation}/qualifications/{qualification}/reject` | `QualificationController@reject` | `observation` (optional) | Redirect back |

**Diplômes**
| Method | Path | Action | Request params | Response |
| --- | --- | --- | --- | --- |
| GET | `/admin/formations/{formation}/diplomes` | `DiplomeController@index` | route param `formation` | Blade view `admin.diplomes.index` |
| GET | `/admin/formations/{formation}/diplomes/create` | `DiplomeController@create` | route param `formation` | Blade view `admin.diplomes.create` |
| POST | `/admin/formations/{formation}/diplomes` | `DiplomeController@store` | `formation_id`, `participant_id`, `numero_diplome`, `date_emission`, `date_validite`, `lieu_emission`, `status`, `remarques` | Redirect to `admin.formations.diplomes.show` |
| GET | `/admin/formations/{formation}/diplomes/{diplome}` | `DiplomeController@show` | route params `formation`, `diplome` | Blade view `admin.diplomes.show` |
| GET | `/admin/formations/{formation}/diplomes/{diplome}/edit` | not implemented in controller | n/a | n/a |
| PUT/PATCH | `/admin/formations/{formation}/diplomes/{diplome}` | not implemented in controller | n/a | n/a |
| DELETE | `/admin/formations/{formation}/diplomes/{diplome}` | `DiplomeController@destroy` | route params `formation`, `diplome` | Redirect to `admin.formations.diplomes.index` |
| POST | `/admin/formations/{formation}/diplomes/{diplome}/deliver` | `DiplomeController@deliver` | route params `formation`, `diplome` | Redirect back |
| POST | `/admin/formations/{formation}/diplomes/{diplome}/reject` | `DiplomeController@reject` | `reason` (optional) | Redirect back |
| POST | `/admin/formations/{formation}/diplomes/{diplome}/cancel` | `DiplomeController@cancel` | route params `formation`, `diplome` | Redirect back |

**Licences**
| Method | Path | Action | Request params | Response |
| --- | --- | --- | --- | --- |
| GET | `/admin/formations/{formation}/licences` | `LicenceController@index` | route param `formation` | Blade view `admin.licences.index` |
| GET | `/admin/formations/{formation}/licences/create` | `LicenceController@create` | route param `formation` | Blade view `admin.licences.create` |
| POST | `/admin/formations/{formation}/licences` | `LicenceController@store` | `formation_id`, `participant_id`, `numero_licence`, `date_emission`, `date_expiration`, `lieu_emission`, `status`, `remarques` | Redirect to `admin.formations.licences.show` |
| GET | `/admin/formations/{formation}/licences/{licence}` | `LicenceController@show` | route params `formation`, `licence` | Blade view `admin.licences.show` |
| GET | `/admin/formations/{formation}/licences/{licence}/edit` | not implemented in controller | n/a | n/a |
| PUT/PATCH | `/admin/formations/{formation}/licences/{licence}` | not implemented in controller | n/a | n/a |
| DELETE | `/admin/formations/{formation}/licences/{licence}` | `LicenceController@destroy` | route params `formation`, `licence` | Redirect to `admin.formations.licences.index` |
| POST | `/admin/formations/{formation}/licences/{licence}/deliver` | `LicenceController@deliver` | route params `formation`, `licence` | Redirect back |
| POST | `/admin/formations/{formation}/licences/{licence}/renew` | `LicenceController@renew` | `date_expiration` | Redirect back |
| POST | `/admin/formations/{formation}/licences/{licence}/suspend` | `LicenceController@suspend` | `reason` (optional) | Redirect back |

**Justificatifs**
| Method | Path | Action | Request params | Response |
| --- | --- | --- | --- | --- |
| GET | `/admin/formations/{formation}/justificatifs` | `JustificatifController@index` | route param `formation` | Blade view `admin.justificatifs.index` |
| GET | `/admin/formations/{formation}/justificatifs/create` | `JustificatifController@create` | route param `formation` | Blade view `admin.justificatifs.create` |
| POST | `/admin/formations/{formation}/justificatifs` | `JustificatifController@store` | `formation_id`, `participant_id`, `diplome_id`, `licence_id`, `type_document`, `nom_fichier`, `chemin_fichier`, `taille_fichier`, `date_ajout`, `is_verified`, `verified_at`, `remarques` | Redirect to `admin.formations.justificatifs.show` |
| GET | `/admin/formations/{formation}/justificatifs/{justificatif}` | `JustificatifController@show` | route params `formation`, `justificatif` | Blade view `admin.justificatifs.show` |
| GET | `/admin/formations/{formation}/justificatifs/{justificatif}/edit` | not implemented in controller | n/a | n/a |
| PUT/PATCH | `/admin/formations/{formation}/justificatifs/{justificatif}` | not implemented in controller | n/a | n/a |
| DELETE | `/admin/formations/{formation}/justificatifs/{justificatif}` | `JustificatifController@destroy` | route params `formation`, `justificatif` | Redirect to `admin.formations.justificatifs.index` |
| POST | `/admin/formations/{formation}/justificatifs/{justificatif}/verify` | `JustificatifController@verify` | route params `formation`, `justificatif` | Redirect back |
| POST | `/admin/formations/{formation}/justificatifs/{justificatif}/reject` | `JustificatifController@reject` | `reason` (optional) | Redirect back |

**Reports**
| Method | Path | Action | Request params | Response |
| --- | --- | --- | --- | --- |
| GET | `/admin/reports/dashboard` | `ReportController@dashboard` | none | Blade view `admin.reports.dashboard` |
| GET | `/admin/formations/{formation}/report` | `ReportController@formationReport` | route param `formation` | Blade view `admin.reports.formation` |
| GET | `/admin/formations/{formation}/situation-sce` | `ReportController@situationSCE` | route param `formation` | Blade view `admin.reports.situation-sce` |
| GET | `/admin/reports/qualification-rate` | `ReportController@qualificationRate` | none | Blade view `admin.reports.qualification-rate` |
| GET | `/admin/reports/diplomation-rate` | `ReportController@diplomationRate` | none | Blade view `admin.reports.diplomation-rate` |
| GET | `/admin/reports/licence-rate` | `ReportController@licenceRate` | none | Blade view `admin.reports.licence-rate` |
| GET | `/admin/reports/attendance-rate` | `ReportController@attendanceRate` | none | Blade view `admin.reports.attendance-rate` |
| GET | `/admin/formations/{formation}/report/export` | `ReportController@exportFormationReport` | route param `formation` | JSON response |

**Permissions**
All admin routes pass through middleware `auth` and `role:admin,super_admin`. Policy methods below apply in addition.

| Resource | Action | Policy method | Allowed roles |
| --- | --- | --- | --- |
| Formation | add (create/store) | `FormationPolicy@create` | `admin`, `super_admin` |
| Formation | edit/save (update) | `FormationPolicy@update` | `admin`, `super_admin` |
| Formation | delete | `FormationPolicy@delete` | `admin`, `super_admin` |
| Formation | execute | `FormationPolicy@execute` | `admin`, `super_admin` |
| Formation | cancel | `FormationPolicy@cancel` | `admin`, `super_admin` |
| Formation | archive | `FormationPolicy@archive` | `admin`, `super_admin` |
| Formation | print/export | not found (export uses `FormationPolicy@view`) | `admin`, `super_admin` |
| Classe | add | `ClassePolicy@create` | `admin`, `super_admin` |
| Classe | edit/save | `ClassePolicy@update` | `admin`, `super_admin` |
| Classe | delete | `ClassePolicy@delete` | `super_admin` |
| Participant | add | `ParticipantPolicy@create` | `admin`, `super_admin` |
| Participant | edit/save | `ParticipantPolicy@update` | `admin`, `super_admin` |
| Participant | delete | `ParticipantPolicy@delete` | `admin`, `super_admin` |
| Participant | assign classe | `ParticipantPolicy@assignToClasse` | `admin`, `super_admin` |
| Participant | activate/deactivate | `ParticipantPolicy@deactivate` | `admin`, `super_admin` |
| Qualification | add | `QualificationPolicy@create` | `admin`, `super_admin` |
| Qualification | edit/save | `QualificationPolicy@update` | `admin`, `super_admin` |
| Qualification | delete | `QualificationPolicy@delete` | `super_admin` |
| Qualification | approve/reject | `QualificationPolicy@approve` / `@reject` | `admin`, `super_admin` |
| Diplôme | add | `DiplomePolicy@create` | `admin`, `super_admin` |
| Diplôme | edit/save | `DiplomePolicy@update` | `admin`, `super_admin` |
| Diplôme | delete | `DiplomePolicy@delete` | `super_admin` |
| Diplôme | deliver/reject/cancel | `DiplomePolicy@deliver` / `@reject` / `@cancel` | `admin`, `super_admin` |
| Licence | add | `LicencePolicy@create` | `admin`, `super_admin` |
| Licence | edit/save | `LicencePolicy@update` | `admin`, `super_admin` |
| Licence | delete | `LicencePolicy@delete` | `super_admin` |
| Licence | deliver/renew/suspend | `LicencePolicy@deliver` / `@renew` / `@suspend` | `admin`, `super_admin` |
| Justificatif | add | `JustificatifPolicy@create` | `admin`, `super_admin` |
| Justificatif | edit/save | `JustificatifPolicy@update` | `admin`, `super_admin` |
| Justificatif | delete | `JustificatifPolicy@delete` | `super_admin` |
| Justificatif | verify/reject | `JustificatifPolicy@verify` / `@reject` | `admin`, `super_admin` |
| Reports (SCE) | view | `FormationPolicy@viewAny` / `@view` | `admin`, `super_admin` |
| Niveau/Catégorie/Sous-catégorie/Instructeur/Examinateur | CRUD | not found | not found |

**Lifecycles**
```mermaid
stateDiagram-v2
    [*] --> "planifiée"
    "planifiée" --> "à programmer": programFormation()
    "à programmer" --> "exécutée": executeFormation()
    "planifiée" --> "annulée": cancelFormation()
    "à programmer" --> "annulée": cancelFormation()
    "exécutée" --> "archivée": archiveFormation()
```
```mermaid
stateDiagram-v2
    [*] --> "à livrer"
    "à livrer" --> "livrée": deliverLicence()
    "à livrer" --> "suspendue": suspendLicence()
    "livrée" --> "renouvelée": renewLicence()
    "suspendue" --> "renouvelée": renewLicence()
    "à livrer" --> "expirée": checkExpiredLicences()
    "livrée" --> "expirée": checkExpiredLicences()
    "suspendue" --> "expirée": checkExpiredLicences()
```

**Validation**
| Entity | Key validation rules (FormRequests) |
| --- | --- |
| Formation | `code` unique, `date_fin_prevue` after `date_debut_prevue`, `prix` numeric, `nombreParticipantsMin/Max` min 1 |
| Classe | `capacite_maximal` min 1, `date_fin` after `date_debut` |
| Participant | `email` unique, `numero_identite` unique, `date_fin` after `date_debut` |
| Qualification | `status` in `qualifié/non qualifié/en attente/refusé`, `score` 0‑100 |
| Diplôme | `numero_diplome` unique, `date_validite` after or equal `date_emission` |
| Licence | `numero_licence` unique, `date_expiration` after `date_emission` |
| Justificatif | `type_document` in `attestation/certificat/relevé/autre`, `taille_fichier` min 1 |

**Audit Logging**
Audit logs are written to `audit_logs` via `App\Models\AuditLog` in these services: `FormationService`, `ClasseService`, `ParticipantService`, `QualificationService`, `DiplomeService`. Each write records `user_id`, `method`, `path`, `action`, `model_type`, `model_id`, `response_code`.

**Extension Points**
| Area | Extension idea |
| --- | --- |
| Reference data | Add CRUD controllers, FormRequests, routes, and views for `niveaux`, `categories`, `sous_categories`, `instructeurs`, `examinateurs`. |
| Reporting | Implement PDF export in `ReportController@exportFormationReport`. |
| Attendance | Add a presence tracking model and relation to complete `ParticipantService::getTauxAssiduite()` and `ReportService::calculateAverageAttendance()`. |
| Scheduling | Expose `FormationService::programFormation()` with a route and UI. |
| Licences | Schedule `LicenceService::checkExpiredLicences()` via a cron/queue job. |

**Gotchas**
| Area | Detail |
| --- | --- |
| Missing views | Controllers reference Blade views under `resources/views/admin/*` for formations, classes, participants, qualifications, diplomes, licences, justificatifs, reports, but only `resources/views/admin/dashboard.blade.php` exists. |
| Resource routes without methods | Resource routes generate `edit` and `update` for qualifications, diplomes, licences, justificatifs, but those controller methods are not implemented. |
| Request vs DB mismatch | `StoreDiplomeRequest`, `StoreLicenceRequest`, `StoreJustificatifRequest` use field names that do not map 1:1 to DB columns (`date_emission`, `date_validite`, `lieu_emission`, `remarques`, `type_document`, `taille_fichier`, `date_ajout`). Services do not map most of these fields. |
| Status overwritten | `QualificationService::recordQualification()`, `DiplomeService::createDiplome()`, `LicenceService::createLicence()` override `status` regardless of input, yet status is required in the FormRequests. |
| Formation/Classe validation | `StoreClasseRequest` requires `formation_id`, but controller supplies it after validation, so form input must include it or validation fails. |
| ReportService enum cases | `ReportService` references `LicenceStatus::Expirée` and `LicenceStatus::À_livrer`, but enum cases are `Expiree` and `ADelivrer`. |
| SituationSCE persistence | `ReportService::generateSituationSCE()` writes keys like `total_participants` and `taux_*` that do not exist in `situation_sces`. |
| Participant attendance | `ParticipantService::getTauxAssiduite()` calls `$participant->presences()` but the relation is not defined. |
| Diplôme documents | `Diplome::justificatifs()` is `belongsTo`, but `justificatifs` uses `diplome_id` and should be `hasMany`. |
| Justificatif verification | `JustificatifService` writes `verified_by` from `auth()->user()->id`, but the FK points to `instructeurs`. It also writes fields (`auditable_type`, `auditable_id`, `old_values`, `new_values`) that do not exist in `audit_logs`. |
| Storage disk | `JustificatifService` uses `Storage::disk('private')` but `config/filesystems.php` defines `local`, `public`, `s3` only. |

**File Map**
| Area | Files |
| --- | --- |
| Models | `app/Models/Formation/*` |
| Enums | `app/Enums/Formation/FormationStatus.php`, `app/Enums/Qualification/QualificationStatus.php`, `app/Enums/Diplome/DiplomeStatus.php`, `app/Enums/Licence/LicenceStatus.php` |
| Controllers | `app/Http/Controllers/Formation/*` |
| FormRequests | `app/Http/Requests/Formation/*` |
| Services | `app/Services/Formation/*` |
| Policies | `app/Policies/*Policy.php` |
| Routes | `routes/web.php` |
| Migrations | `database/migrations/2026_02_15_04*.php` |
