<?php

namespace App\Livewire\HumanOps\Privacy;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class Index extends Component
{
    public $privacyRules = [];
    public $dataFlowInfo = [];

    public function mount(): void
    {
        $this->loadPrivacyInfo();
    }

    protected function loadPrivacyInfo()
    {
        $this->privacyRules = [
            [
                'rule' => 'Minimum Group Size',
                'description' => 'Data is only shown for groups of 10 or more employees',
                'reason' => 'Prevents identification of individual employees',
                'status' => 'enforced',
            ],
            [
                'rule' => '48-Hour Delay',
                'description' => 'Check-in data is delayed by 48 hours before appearing in HumanOps',
                'reason' => 'Prevents real-time tracking of employee emotional states',
                'status' => 'enforced',
            ],
            [
                'rule' => 'Anonymization',
                'description' => 'All employee data is aggregated and anonymized',
                'reason' => 'Individual identities are never exposed to employers',
                'status' => 'enforced',
            ],
            [
                'rule' => 'Department-Level Only',
                'description' => 'Data is aggregated at department level, not individual level',
                'reason' => 'Protects individual privacy while providing organizational insights',
                'status' => 'enforced',
            ],
            [
                'rule' => 'No Individual Access',
                'description' => 'Employers cannot view individual employee check-ins',
                'reason' => 'WorkBalance is a private, safe space for employees',
                'status' => 'enforced',
            ],
            [
                'rule' => 'Voluntary Participation',
                'description' => 'Employees can choose whether to check in',
                'reason' => 'No one is required to share their mental health data',
                'status' => 'enforced',
            ],
        ];

        $this->dataFlowInfo = [
            'step1' => [
                'title' => 'Employee Checks In',
                'description' => 'Employee uses WorkBalance to record stress, energy, and mood privately',
                'privacy_level' => 'Maximum Privacy',
            ],
            'step2' => [
                'title' => 'Data Storage',
                'description' => 'Data is encrypted and stored securely. Only the employee can see their individual data.',
                'privacy_level' => 'Encrypted Storage',
            ],
            'step3' => [
                'title' => '48-Hour Delay',
                'description' => 'Data waits 48 hours before being eligible for aggregation',
                'privacy_level' => 'Time Delay Protection',
            ],
            'step4' => [
                'title' => 'Aggregation',
                'description' => 'Data is grouped with at least 9 other employees (minimum 10 total)',
                'privacy_level' => 'Group Anonymization',
            ],
            'step5' => [
                'title' => 'HumanOps Display',
                'description' => 'Only aggregated, anonymized patterns appear in HumanOps Intelligence',
                'privacy_level' => 'Anonymized Insights',
            ],
        ];
    }

    public function render(): View
    {
        return view('livewire.humanops.privacy.index');
    }
}
