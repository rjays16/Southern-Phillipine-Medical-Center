<?php
use SegHEIRS\modules\integrations\modules\jnj\services\ORMO01Handler;

class TestController extends Controller
{

    public function actionIndex()
    {
        die("Testing this controller!");
    }    
    
//    public function actionOrmCreate()
//    {
//        $message = (new ORMO01Factory())->createOrder(\LaborderH::model()->findByPk($_GET['id']));
//        $writer = new OutgoingMessageWriter();
////        sd($message->toString());
//        $writer->write($message);
//    }
    
//
//    public function actionViewOrmCreate()
//    {
//        $message = (new ORMO01Factory())->createOrder(\LaborderH::model()->findByPk($_GET['id']));
//        $writer = new OutgoingMessageWriter();
//        sd($message->toString());
//        $writer->write($message);
//    }
//
//    public function actionOrmUpdate()
//    {
//        $message = (new ORMO01Factory())->updateOrder(\LaborderH::model()->findByPk($_GET['id']));
//        $writer = new OutgoingMessageWriter();
//        $writer->write($message);
//    }
//
//    public function actionOrmCancel()
//    {
//        $message = (new ORMO01Factory())->cancelOrder(\RadorderD::model()->findByPk($_GET['id']));
//        $writer = new OutgoingMessageWriter();
//        $writer->write($message);
//    }
//
    public function actionOrmProcess()
    {
        $message = "MSH|^~\&|medavis RIS|MEDAVIS|MMIS|MAAYO|20170330144432||ORM^O01|139|P|2.3.1|||AL|NE||8859/1
PID|1|1700000003^^^MEDAVIS^PI|1700000003^^^MMIS^PT|170328000002|CARALOS^JOHN ALVIN||19520115|F|||^^Tuburan^^6043^PH|||||||||||||||||||N
NTE|1||58d9dacca35d3 03.30.2017|CASE-DESCRIPTION
PV1|1|O|Triage 1|||||3^Maayo Clinic^^^^^^^MEDAVIS|||||||||||170328000002^^^^VN|||||Patient||||||||||||||||||||20170328012800|||||||V
IN1|1||0000^^^^NII~00000^^^^NIIP||||||||||||||||||||||||||||||||||^TP
ORC|CA|1029a8ae-1368-11e7-9e57-00505686fadb|2^MEDAVIS|58d9dacca35d3|CA||1^once^^20170330144400^20170330144900^R||20170328113800|2^medavis^GmbH^M.D^^^^^^^^^PN||3^Maayo Clinic
OBR|1|1029a8ae-1368-11e7-9e57-00505686fadb|2^MEDAVIS|CXRPA^CHEST PA^MEDAVIS^CXRPA^CHEST PA^MEDAVIS|||20170330144400|20170330144900||||||||3^Maayo Clinic||U-ID16|MOBXR200||||||S||1^once^^20170330144400^20170330144900^1|||WALK||||||20170330144400||||||N|^^^^MOBILE X-RAY||||CXRPA^CHEST PA^MEDAVIS
NTE||||EXAM-DESCRIPTION
";
        $service = new ORMO01Handler();
        $service->processMessage(new Message($message));
    }

    public function actionOruProcess()
    {
        $message = "MSH|^~\&|medavis RIS|MEDAVIS|MMIS|MAAYO|20170330173455||ORU^R01|156|P|2.3.1|||AL|NE||8859/1|
PID|1|1700000003^MEDAVIS^PI||170328000002|CARALOS^JOHN ALVIN||19520115|F|||Tuburan^6043^PH|||||||||||||||||||N|
PV1|1|O|Triage 1|||||3^Maayo Clinic^MEDAVIS|||||||||||170328000002^VN|||||Patient||||||||||||||||||||20170328012800|||||||V|
ORC|RE|1029a8ae-1368-11e7-9e57-00505686fadb|2^MEDAVIS|58d9dacca35d3|CM||1^once^20170330172000^20170330172000^R||20170328113800|2^medavis^GmbH^M.D^PN||3^Maayo Clinic|
OBR|1|1029a8ae-1368-11e7-9e57-00505686fadb|2^MEDAVIS|CXRPA^CHEST PA^MEDAVIS^CXRPA^CHEST PA^MEDAVIS|||20170330172000|20170330172000||||||||3^Maayo Clinic||U-ID16|MOBXR200|||20170330173400|||C||1^once^20170330172000^20170330172000^1|||WALK||2^medavis^GmbH^^^M.D^^^MEDAVIS|2^medavis^GmbH^^^M.D^^^MEDAVIS|2^medavis^GmbH^^^M.D^^^MEDAVIS||20170330144400||||||N|MOBILE X-RAY||||CXRPA^CHEST PA^MEDAVIS|
OBX|1|HD|SR Instance UID||1.2.276.0.37.1.380.20161288.9|||N|||C|||||2^medavis^GmbH^M.D^PN|
OBX|2|EI|Document Identifier||9^MEDAVIS|||N|
OBX|3|HD|Study Instance UID|1|1.2.276.0.37.1.380.20161216|||N|||C|||||2^medavis^GmbH^M.D^PN|
OBX|4|TX|SR Text||interpretation:test report for HIS RIS integration..\\X0D\\\\X0A\\Hehehehe\\X0D\\\\X0A\\impression:Not impressed\\X0D\\\\X0A\\Definitely not impressed\\X0D\\\\X0A\\|||N|||C|||||2^medavis^GmbH^M.D^PN|
";
        $service = new ORUR01Handler();
        $service->processMessage(new Message($message));
    }
//
//    public function actionCopy()
//    {
//        $message = "MSH|^~\&|extSys||medavis RIS||19981124184044||ORM^O01|242|P|2.2|||AL|NE
//PID|||432353|5423523|Stalone^Stefan||19400316|M|||Sesamstr.15^^Waldwiesental^^32221^D|09182119|0721/92910-0|||NV|RK||||||Karlsruhe|||D
//PV1||S|3^024^2^IN5|N|||||||||||||||44821||||||||||||||||||||931004|||||19981120102600||||||44821
//ORC|NW|110228||15405044|10||^^^^19981125183500^R||19981124183517|UNIUSER||999^uni-user
//OBR|1|110228||3764^Schilddrüsen-Szintigraphie^KAT-MEDAVIS|110228
//ORC|NW|110229||15405044|10||^^^^19981125183500^R||19981124183517|UNIUSER||999^uni-user
//OBR|2|110229||4711^Thorax^KAT-MEDAVIS|110229
//OBX|1|NM|^CREATININE^LN||0.600000||||||R
//OBX|2|NM|^TSH^LN||0.590000||||||R DG1|1||K92.2^Gastrointestinal haemorrhage, unspecified^I10 ||19981123152300|AD|||||||||1.1|0004711
//";
//        $message = new Message($message);
//        $pid = PID::createFromSegment($message->getSegmentsByName('PID')[0]);
//        ddd($pid);
//    }
//
//    /**
//     *
//     */
//    public function actionWrite()
//    {
//        $message = "MSH|^~\&|MMIS|Maayo|medavis RIS|MEDAVIS|20170330162002||ORM^O01|58dcbfb295a44||2.2|
//PID|||1700000002||BALUYOT^GERARD^ANGELO^^||19920117|M|||^^Tuburan^Cebu^6043^PH^^|
//PV1|1|O|Outpatient Department||||||||||||||||170330000002|||||||||||||||||||||||||20170330161630|||||||V|
//ORC|NW|4abeac3c-1521-11e7-b450-a45d36179d56||58dcbf0ef0edc|IP||1^^^^^R||20170330161719|201600000034^Batuto^Javeson Roy ^M.|
//OBR|1|4abeac3c-1521-11e7-b450-a45d36179d56||MRWHLBDPLN^WHOLE BODY MRI PLAIN|R|
//";
//        $writer = new MessageWriter(new Message($message));
//        $writer->write();
//    }
//
//    public function actionCollector()
//    {
//        $collector = new IncomingMessageCollector();
//        $messages = $collector->collect();
//
//        foreach ($messages as $message) {
//            $router = new MessageRouter();
//            $router->processMessage($message);
//        }
//
//    }
}