<?php

/**
 * CommentType.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2017, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\codes;

/**
 *
 * Description of CommentType
 *
 */

class CommentType
{

    const PATIENT_INSTRUCTIONS = 'PI';
    const ANCILLARY_INSTRUCTIONS = 'AI';
    const GENERAL_INSTRUCTIONS = 'GI';
    const PRIMARY_REASON = '1R';
    const SECONDARY_REASON = '2R';
    const GENERAL_REASON = 'GR';
    const REMARK = 'RE';
    const DUPLICATE_INTERACTION_REASON = 'DR';

}
