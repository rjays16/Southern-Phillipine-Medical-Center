<?php
namespace SegHis\modules\socialService\models;

use SegHis\modules\person\models\Encounter;
use SegHis\modules\personnel\models\Personnel;
use SegHis\modules\personnel\models\PersonnelDependent;

class CharityGrant
{
    const DISCOUNT_SENIOR_CITIZEN = 'SC';
    const DISCOUNT_PERSONNEL_HEALTH_SERVICE = 'PHS';

    public static function getDiscount($pid, $encounterNr)
    {
        $personnel = Personnel::findActivePersonnelByPid($pid);
        $personnelDependent = PersonnelDependent::findActiveDependentByPid($pid);
        $encounter = Encounter::findActiveEncounterNrByPid($pid);
        $personCharityGrant = self::findByPid($pid);
        $encounterCharityGrant = self::findByEncounter($encounterNr);
        return self::_getDiscount($personnel, $personnelDependent, $encounter, $personCharityGrant, $encounterCharityGrant);
    }

    /**
     * @param $personnel Personnel
     * @param $personnelDependent PersonnelDependent
     * @param $encounter Encounter
     * @param $personCharityGrant PersonCharityGrant
     * @param $encounterCharityGrant EncounterCharityGrant
     * @return array
     */
    public static function _getDiscount($personnel, $personnelDependent, $encounter, $personCharityGrant, $encounterCharityGrant)
    {
        if ($personnel || $personnelDependent) {
            return array(
                'id' => $encounter ? self::DISCOUNT_PERSONNEL_HEALTH_SERVICE : null,
                'percentage' => $encounter ? 1 : 0
            );
        }

        if ($personCharityGrant->discountid == static::DISCOUNT_SENIOR_CITIZEN) {
            if ($encounter) {
                $discountPercentage = 1;
            } else {
                $discountPercentage = 0.2;
            }
            return array(
                'id' => static::DISCOUNT_SENIOR_CITIZEN,
                'percentage' => $discountPercentage
            );
        }

        if (!$encounter) {
            return array(
                'id' => null,
                'percentage' => 0
            );
        }

        if ($encounter->encounter_type == Encounter::ENCOUNTER_TYPE_OPD) {
            return array(
                'id' => $personCharityGrant->discountid,
                'percentage' => $personCharityGrant->discount
            );
        } else {
            return array(
                'id' => $encounterCharityGrant->discountid,
                'percentage' => $encounterCharityGrant->discount
            );
        }
    }

    /**
     * @param $pid
     * @return PersonCharityGrant|null
     */
    public static function findByPid($pid)
    {
        return PersonCharityGrant::model()->findByAttributes(array(
            'pid' => $pid
        ));
    }

    /**
     * @param $encounterNr
     * @return EncounterCharityGrant|null
     */
    public static function findByEncounter($encounterNr)
    {
        return EncounterCharityGrant::model()->findByAttributes(array(
            'encounter_nr' => $encounterNr
        ));
    }

}