<?php

use App\Models\Applicant;
use App\Models\Application;
use App\Models\ApplicationAnswer;
use App\Models\ApplicationInvitation;
use App\Models\ApplicationStatusHistory;
use App\Models\Department;
use App\Models\EmploymentType;
use App\Models\FormField;
use App\Models\FormFieldOption;
use App\Models\FormSection;
use App\Models\FormTemplate;
use App\Models\FormVersion;
use App\Models\JobHiring;
use App\Models\Location;
use App\Models\Onboarding;
use App\Models\OnboardingInvitation;
use App\Models\OnboardingRequireInstance;
use App\Models\OnboardingRequirement;
use App\Models\OnboardingTemplate;
use App\Models\OnboardingTemplateVersion;
use App\Models\RequirementReview;
use App\Models\RequirementSubmission;
use App\Models\SubmissionFile;
use Illuminate\Database\Eloquent\Model;

test('models explicitly whitelist mass-assignable attributes', function (string $modelClass) {
    /** @var Model $model */
    $model = new $modelClass;

    expect($model->getFillable())
        ->not->toBeEmpty()
        ->not->toContain('id', 'created_at', 'updated_at')
        ->and($model->getGuarded())
        ->toBe(['*']);
})->with([
    'Applicant' => Applicant::class,
    'Application' => Application::class,
    'Application answer' => ApplicationAnswer::class,
    'Application invitation' => ApplicationInvitation::class,
    'Application status history' => ApplicationStatusHistory::class,
    'Department' => Department::class,
    'Employment type' => EmploymentType::class,
    'Form field' => FormField::class,
    'Form field option' => FormFieldOption::class,
    'Form section' => FormSection::class,
    'Form template' => FormTemplate::class,
    'Form version' => FormVersion::class,
    'Job hiring' => JobHiring::class,
    'Location' => Location::class,
    'Onboarding' => Onboarding::class,
    'Onboarding invitation' => OnboardingInvitation::class,
    'Onboarding requirement instance' => OnboardingRequireInstance::class,
    'Onboarding requirement' => OnboardingRequirement::class,
    'Onboarding template' => OnboardingTemplate::class,
    'Onboarding template version' => OnboardingTemplateVersion::class,
    'Requirement review' => RequirementReview::class,
    'Requirement submission' => RequirementSubmission::class,
    'Submission file' => SubmissionFile::class,
]);
