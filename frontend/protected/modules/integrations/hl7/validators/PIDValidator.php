<?php

/**
 * PIDValidator.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2017, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\validators;
use PatientCatalog;
use SegHEIRS\modules\integrations\hl7\exceptions\HL7RecordNotFoundException;
use SegHEIRS\modules\integrations\hl7\segments\PID;

/**
 *
 * Description of PIDValidator
 *
 */

class PIDValidator
{
    /**
     * @var PID
     */
    protected $pid;

    /**
     * PIDValidator constructor.
     */
    public function __construct(PID $pid)
    {
        $this->pid = $pid;
    }

    /**
     *
     * @throws HL7RecordNotFoundException
     *
     */
    public function validate()
    {
        $spin = $this->pid->getPatientId();
        if ($spin) {
            $spin = (array) $spin;
        } else {
            $spin = (array) $this->pid->getPatientIdentifierList();
        }

        if (!PatientCatalog::model()->exists([
            'condition' => 'spin=:spin',
            'params' => [
                ':spin' => '20' . current($spin)
            ]
        ])) {
            throw new HL7RecordNotFoundException('Patient record not found');
        };
    }

}

