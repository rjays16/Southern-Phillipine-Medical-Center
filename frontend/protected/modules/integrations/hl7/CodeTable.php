<?php

/**
 * CodeTable.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2017, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7;

/**
 *
 * Description of CodeTable
 *
 */

class CodeTable
{
    /** 0008 – Acknowledgment Code */
    const ACK_CODE_ACCEPT = 'AA';
    const ACK_CODE_ERROR = 'AE';
    const ACK_CODE_REJECT = 'AR';
    const ACK_CODE_COMMIT_ACCEPT = 'CA';
    const ACK_CODE_COMMIT_ERROR = 'CE';
    const ACK_CODE_COMMIT_REJECT = 'CR';

    /** Order Status Modifier */
    const ORDER_STATUS_MODIFIER_IN_PROGRESS = 'I';
    const ORDER_STATUS_MODIFIER_ABORTED = 'S';
    const ORDER_STATUS_MODIFIER_PRELIMINARY_REPORT = 'P';
    const ORDER_STATUS_MODIFIER_FINALIZED_REPORT = 'F';
    const ORDER_STATUS_MODIFIER_COMPLETED_REPORT = 'C';

    /** Comment type */
    const COMMENT_TYPE_PATIENT_INSTRUCTIONS = 'PI';
    const COMMENT_TYPE_ANCILIARY_INSTRUCTIONS= 'AI';
    const COMMENT_TYPE_GENERAL_INSTRUCTIONS= 'GI';
    const COMMENT_TYPE_PRIMARY_REASON = '1R';
    const COMMENT_TYPE_SECONDARY_REASON = '2R';
    const COMMENT_TYPE_GENERAL_REASON = 'GR';
    const COMMENT_TYPE_REMARK = 'RE';
    const COMMENT_TYPE_DUPLICATE_REASON = 'DR';

    /** 0357 – Message Error Condition Codes */
    const ERROR_MESSAGE_ACCEPTED = 0;
    const ERROR_SEGMENT_SEQUENCE_ERROR = 100;
    const ERROR_REQUIRED_FIELD_MISSING = 101;
    const ERROR_DATA_TYPE_ERROR = 102;
    const ERROR_TABLE_VALUE_NOT_FOUND = 103;
    const ERROR_VALUE_TOO_LONG = 104;
    const ERROR_UNSUPPORTED_MESSAGE_TYPE = 200;
    const ERROR_UNSUPPORTED_EVENT_CODE = 201;
    const ERROR_UNSUPPORTED_PROCESSING_ID = 202;
    const ERROR_UNSUPPORTED_VERSION_ID = 203;
    const ERROR_UNKNOWN_KEY_IDENTIFIER = 204;
    const ERROR_APPLICATION_RECORD_LOCKED = 206;
    const ERROR_APPLICATION_INTERNAL_ERROR = 207;

    /** 0516 - Error Severity */
    const SEVERITY_WARNING = 'W';
    const SEVERITY_INFO = 'W';
    const SEVERITY_ERROR = 'E';
    const SEVERITY_FATAL_ERROR = 'F';
}