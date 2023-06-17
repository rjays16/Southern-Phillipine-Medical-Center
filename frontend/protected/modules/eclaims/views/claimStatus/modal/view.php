<? if ($success): ?>
    <div class="alert alert-info">
        <strong>Success : </strong> <?php echo $message; ?>
    </div>
<? else: ?>

    <div class="alert alert-danger">
        <strong>Error: </strong> <?php echo $message; ?>
    </div>
<? endif; ?>

<div class="row-fluid">


    <div class="span6">
        <h5>Admission Details</h5>
        <?php
        $this->widget(
            'bootstrap.widgets.TbDetailView',
            array(
                'data'       => $claim->encounter,
                'attributes' => array(
                    array('name' => 'person.phicMember.insurance_nr', 'label' => 'Member PIN'),
                    array('name' => 'person.name_last'),
                    array('name' => 'person.name_first'),
                    array('name' => 'person.name_middle'),
                    array('name' => 'person.FullAddress'),
                    array('name' => 'admissionDt', 'label' => 'Admission Date', 'type' => 'date'),
                    array('name' => 'discharge_date', 'label' => 'Discharge Date', 'type' => 'date'),
                ),
                'type'       => 'striped condensed bordered',
            )
        );
        ?>
    </div>
    <div class="span6">
        <h5>Claim Details</h5>
        <?php
        $this->widget(
            'bootstrap.widgets.TbDetailView',
            array(
                'data'       => $claim,
                'attributes' => array(
                    array('name' => 'status.status'),
                    array('name' => 'status.as_of', 'label' => 'Date of Inquiry', 'type' => 'datetime'),
                    array('name' => 'claim_series_lhio', 'label' => 'Claim Series Number'),
                    array('name' => 'status.claim_date_received', 'label' => 'Date Received'),
                    array('name' => 'status.claim_date_refile', 'label' => 'Date Refile'),
                ),
                'type'       => 'striped condensed bordered',
            )
        );
        ?>
    </div>
</div>

<div class="row-fluid">
    <div class="span12">
        <?php
        if ($claim->status->status == ClaimStatus::STATUS_IN_PROCESS) {
            $this->renderPartial('statuses/inProcess', array(
                'claim' => $claim,
            ));
        } elseif ($claim->status->status == ClaimStatus::STATUS_RETURN) {
            $this->renderPartial('statuses/return', array(
                'claim' => $claim,
            ));
        } elseif ($claim->status->status == ClaimStatus::STATUS_DENIED) {
            $this->renderPartial('statuses/denied', array(
                'claim' => $claim,
            ));
        } elseif ($claim->status->status == ClaimStatus::STATUS_WITH_VOUCHER ||
            $claim->status->status == ClaimStatus::STATUS_VOUCHERING ||
            $claim->status->status == ClaimStatus::STATUS_WITH_CHEQUE) {
            $this->renderPartial('statuses/withPayment', array(
                'claim' => $claim,
            ));
        }
        ?>
    </div>
</div>
