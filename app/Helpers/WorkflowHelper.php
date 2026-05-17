<?php

namespace App\Helpers;

class WorkflowHelper
{
    /*
    |--------------------------------------------------------------------------
    | Departamentos
    |--------------------------------------------------------------------------
    */

    public const DEPARTMENT_FINANCES = 'Finanzas';

    public const DEPARTMENT_CONTROLLING = 'Controlling';

    /*
    |--------------------------------------------------------------------------
    | Estados Route Planning / Renditions
    |--------------------------------------------------------------------------
    */

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PENDING_JEFATURA = 'pending_jefatura';

    public const STATUS_PENDING_CONTROLLING = 'pending_controlling';

    public const STATUS_PENDING_FINANCES = 'pending_finances';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const ROLE_ADMIN = 'admin';

    public const ROLE_WORKER = 'worker';

    public const ROLE_JEFATURA = 'jefatura';

    public const ROLE_SUPERVISOR = 'supervisor';
}