<?php
/**
 * EventNames.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2017, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\components\event;

/**
 *
 * Keeps track of all event names used by the system.
 *
 * This allows for loose
 * coupling in between modules and at the same time makes it easier to manage
 * events by having a common repository to reference event names without
 * directly referencing related components.
 *
 * Please try to maintain the listing in alphabetical order.
 *
 * @todo List all events for Appointments module
 * @todo List all events for Doctors module
 * @todo List all events for Cashier module
 * @todo List all events for Laboratory module
 * @todo List all events for Radiology module
 * @todo List all events for Pharmacy module
 * @todo List all events for Triage/Nurse module
 *
 * The suggested naming convention for event name constants follow this
 * format:
 *
 *   EVENT_<MODULE>_<SUBJECT>_<ACTION PERFORMED>
 */

class EventNames
{

    /** Core Module */
    const EVENT_ORDERING_ORDER_TYPES_QUERY = 'core.ordering.orderTypes.query';
    const EVENT_WORKFLOW_STATE_CHANGED = 'core.workflow.state.changed';

    /** Appointments module */
    const EVENT_APPOINTMENT_CREATED = 'appointment.created';
    const EVENT_APPOINTMENT_ORDER_CREATED = 'appointment.order.created';
    const EVENT_APPOINTMENT_STATUS_CHANGED = 'appointment.status.changed';

    /** Billing Events */
    const EVENT_BILLING_RECON_CREATED = 'billing.recon.created';
    const EVENT_BILLING_RECON_POSTED = 'billing.recon.posted';
    const EVENT_BILLING_BILL_FINALIZED = 'billing.after.finalize.bill';

    /** Cardiology module */
    const EVENT_CARDIOLOGY_ORDER_CREATED = 'cardiology.order.created';
    const EVENT_CARDIOLOGY_ORDER_UPDATED = 'cardiology.order.updated';
    const EVENT_CARDIOLOGY_ORDER_CANCELLED = 'cardiology.order.cancelled';
    const EVENT_CARDIOLOGY_ORDER_SCHEDULED = 'cardiology.order.scheduled';
    const EVENT_CARDIOLOGY_REQUEST_SERVED = 'cardiology.request.served';

    /** Cashier module events */
    const EVENT_CASHIER_CASH_LEDGER_QUERY = 'cashier.cashLedger.query';
    const EVENT_CASHIER_PAYMENT_MODES_QUERY = 'cashier.paymentModes.query';
    const EVENT_CASHIER_PAYMENT_CREATED = 'cashier.payment.created';
    const EVENT_CASHIER_PAYMENT_CANCELLED = 'cashier.payment.cancelled';

    /** CSR module */
    const EVENT_CSR_DELIVERY_CREATED = 'csr.delivery.created';
    const EVENT_CSR_ADJUSTMENT_CREATED = 'csr.adjustment.created';
    const EVENT_CSR_COST_ADJUSTMENT_CREATED = 'csr.cost.adjustment.created';

    /** Doctors module */
    const EVENT_DOCTOR_ORDER_FINALIZED = 'doctor.order.finalized';

    /** Integrations module */
    const EVENT_INTEGRATIONS_STARTED = 'integrations.started';

    /** Inventory module */
    const EVENT_INVENTORY_TRANSACTION_PROCESSING = 'inventory.transaction.processing';
    const EVENT_INVENTORY_TRANSACTION_PROCESSED = 'inventory.transaction.processed';

    /** Radiology module events */
    const EVENT_RADIOLOGY_RESULT_RELEASED = 'radiology.result.released';
    const EVENT_RADIOLOGY_ORDER_CANCELLED = 'radiology.order.cancelled';
    const EVENT_RADIOLOGY_ORDER_SCHEDULED = 'radiology.order.scheduled';
    const EVENT_RADIOLOGY_ORDER_CREATED = 'radiology.order.created';
    const EVENT_RADIOLOGY_ORDER_UPDATED = 'radiology.order.updated';
    const EVENT_RADIOLOGY_ORDER_WORKLIST_ADDED = 'radiology.order.worklist.added';
    const EVENT_RADIOLOGY_ORDER_WORKLIST_REMOVED = 'radiology.order.worklist.removed';
    const EVENT_NURSE_CARRYOUT_RADIOLOGY = 'nursing.radiologyOrder.carriedOut';
    const EVENT_RADIOLOGY_AFTER_SERVE_REQUEST = 'radiology.after.serve.request';

    /** Laboratory module events */
    const EVENT_LABORATORY_RESULT_RELEASED = 'laboratory.result.released';
    const EVENT_LABORATORY_SPECIMEN_CONFIRMED = 'laboratory.specimen.confirmed';
    const EVENT_LABORATORY_BATCH_SPECIMEN_CONFIRMED = 'laboratory.batch.specimen.confirmed';

    /** Nurse module events */
    const EVENT_NURSE_CARRYOUT_LABORATORY = 'nursing.laboratoryOrder.carriedOut';
    const EVENT_NURSE_PATIENT_ASSESSMENT_CREATED = 'nursing.patientAssessment.created';

    /** AUXLIARY Events */
    const EVENT_NURSE_CARRYOUT_AUXILIARY = 'nursing.auxiliaryOrder.carriedOut';
    const EVENT_AUX_REQUEST_SERVED = 'aux.request.served';

    /** Pharmacy module */
    const EVENT_PHARMACY_ORDER_SERVED = 'pharmacy.order.served';
    const EVENT_PHARMACY_RETURN_CREATED = 'pharmacy.return.created';

    /** AUXLIARY-DNC Events */
    const EVENT_DNC_REQUEST_SERVED = 'dnc.request.served';

    /** AUXLIARY-Hepatology Events */
    const EVENT_HEPATOLOGY_REQUEST_SERVED = 'hepatology.request.served';

    /** AUXLIARY-ophtha Events */
    const EVENT_OPHTHALMOLOGY_REQUEST_SERVED = 'ophthalmology.request.served';

    /** AUXLIARY-WHC Events */
    const EVENT_WHC_REQUEST_SERVED = 'whc.request.served';

    /** AUXLIARY-ENT Events */
    const EVENT_ENT_REQUEST_SERVED = 'ent.request.served';

    /** AUXLIARY-DENTAL Events */
    const EVENT_DENTAL_REQUEST_SERVED = 'dental.request.served';

    /** AUXLIARY-REHAB Events */
    const EVENT_REHAB_REQUEST_SERVED = 'rehab.request.served';

    /** Propery module events */
    const EVENT_PROPERTY_ISSUANCE_RELEASED = 'property.issuance.released';

    /** Triage events */
    const EVENT_TRIAGE_ASSESSMENT_CREATED = 'triage.assessment.created';
    const EVENT_TRIAGE_CONSULTATION_CREATED = 'triage.consultation.created';
    const EVENT_TRIAGE_PATIENT_CREATED = 'triage.patient.created';
    const EVENT_TRIAGE_PATIENT_UPDATED = 'triage.patient.updated';

    /** Notification Events */
    const EVENT_NOTIFICATION_EVENT_HANDLED = 'notification.event.handled';

    /** Patient Kiosk Events */
    const EVENT_PATIENT_KIOSK_LABORATORY_CHECKED_IN = 'patientKiosk.laboratory.checkedIn';
    
    // HL7 Acknowledgement Event 
    const EVENT_HL7_ACKNOWLEDGEMENT = 'hl7.message.acknowledgement';
    
    // HL7 CBG Ordering Event
    const EVENT_CBG_ORDERED = 'lab.CBG.ordered';
}
