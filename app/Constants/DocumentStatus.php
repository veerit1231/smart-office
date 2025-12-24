<?php

namespace App\Constants;

class DocumentStatus
{
    const DRAFT = 'draft';
    const PENDING_CLERK = 'pending_clerk';
    const PENDING_DIRECTOR = 'pending_director';
    const APPROVED = 'approved';
    const REJECTED = 'rejected';
    const DISTRIBUTED = 'distributed';
}
