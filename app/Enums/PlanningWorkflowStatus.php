<?php

namespace App\Enums;

enum PlanningWorkflowStatus: string
{
    case Draft = 'draft';

    case PendingJefatura = 'pending_jefatura';

    case PendingFinance = 'pending_finance';

    case ApprovedFinance = 'approved_finance';

    case Rejected = 'rejected';

    case Completed = 'completed';
}