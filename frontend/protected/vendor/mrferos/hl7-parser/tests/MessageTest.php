<?php

require_once 'PHPUnit/Framework/TestCase.php';

class MessageTest extends PHPUnit_Framework_TestCase {

//    public function test() {}

    public function test() {
        # Simple constructor
        #
        $msg = new HL7\Message();
        $seg1 = new HL7\Segment("PID");

        $seg1->setField(2, "Foo");

        $msg->addSegment(new HL7\Segments\MSH());
        $msg->addSegment($seg1);

        $seg0 = $msg->getSegmentByIndex(0);
        $seg1 = $msg->getSegmentByIndex(1);

        $seg0->setField(3, "XXX");

        $this->assertTrue($seg0->getName() == "MSH", "Segment 0 name MSH");
        $this->assertTrue($seg1->getName() == "PID", "Segment 1 name PID");
        $this->assertTrue($seg0->getField(3) == "XXX", "3d field of MSH");
        $this->assertTrue($seg1->getField(2) == "Foo", "2nd field of PID");

        // Check references
        $segX = $msg->getSegmentByIndex(0);
        $this->assertTrue($segX->getField(3) == "XXX", "3d field of MSH");

        $msg = new HL7\Message("MSH|^~\\&|1|\rPID|||xxx|\r");

        $seg0 = $msg->getSegmentByIndex(0);

        $this->assertTrue($msg->toString() == "MSH|^~\\&|1|\rPID|||xxx|\r", "String representation of message");

        $this->assertTrue($msg->toString(1) == "MSH|^~\\&|1|\nPID|||xxx|\n", "Pretty print representation of message");

        $this->assertTrue($seg0->getField(2) == "^~\\&", "Encoding characters (MSH(2))");

        # Constructor with components and subcomponents
        #
        $msg = new HL7\Message("MSH|^~\\&|1|\rPID|||xx^x&y&z^yy^zz|\r");

        $seg1 = $msg->getSegmentByIndex(1);
        $comps = $seg1->getField(3);

        $this->assertTrue($comps[0] == "xx", "Composed field");
        $this->assertTrue($comps[1][1] == "y", "Subcomposed field");

        # Trying different field separator
        #
        $msg = new HL7\Message("MSH*^~\\&*1\rPID***xxx\r");

        $this->assertTrue($msg->toString() == "MSH*^~\&*1*\rPID***xxx*\r", "String representation of message with * as field separator");

        $seg0 = $msg->getSegmentByIndex(0);

        $this->assertTrue($seg0->getField(3) == "1", "3d field of MSH");

        # Trying different field sep and control chars
        #
        $msg = new HL7\Message("MSH*.%#@*1\rPID***x.x@y@z.z\r");

        $seg1 = $msg->getSegmentByIndex(1);
        $comps = $seg1->getField(3);

        $this->assertTrue($comps[0] == "x", "Composed field with . as separator");
        $this->assertTrue($comps[1][1] == "y", "Subcomposed field with @ as separator");

        # Faulty constuctor
        #
        //$this->assertTrue(! defined(new Net::HL7::Message("MSH|^~\\&*1\rPID|||xxx\r")), "Field separator not repeated");

        $seg2 = new HL7\Segment("XXX");

        $msg->addSegment($seg2);

        $msg->removeSegmentByIndex(1);

        $seg1 = $msg->getSegmentByIndex(1);

        $this->assertTrue($seg1->getName() == $seg2->getName(), "Add/remove segment");

        $seg3 = new HL7\Segment("YYY");
        $seg4 = new HL7\Segment("ZZZ");

        $msg->insertSegment($seg3, 1);
        $msg->insertSegment($seg4, 1);

        $seg3 = $msg->getSegmentByIndex(3);

        $seg4 = $msg->getSegmentByIndex(4);

        $this->assertTrue($seg3->getName() == $seg2->getName(), "Insert segment");

        $msg->removeSegmentByIndex(1);
        $msg->removeSegmentByIndex(1);
        $msg->removeSegmentByIndex(6);

        $seg5 = new HL7\Segment("ZZ1");

        $msg->insertSegment($seg5, 2);

        $seg2 = $msg->getSegmentByIndex(2);

        $this->assertTrue($seg2->getName() == $seg5->getName(), "Insert segment");

        $msg->setSegment($seg3, 2);

        $seg2 = $msg->getSegmentByIndex(2);

        $this->assertTrue($seg2->getName() == $seg3->getName(), "Set segment");

        $this->assertTrue(count($msg->getSegmentsByName("MSH")) == 1, "Number of MSH segments");

        $msh2 = new HL7\Segments\MSH();

        $msg->addSegment($msh2);

        $this->assertTrue(count($msg->getSegmentsByName("MSH")) == 2, "Added MSH segment, now two in message");


        # Fumble 'round with ctrl chars
        #
        $msg = new HL7\Message();

        $msh = new HL7\Segments\MSH(array());

        $msh->setField(1, "*");
        $msh->setField(2, "abcd");

        $msg->addSegment($msh);

        $this->assertTrue($msg->toString() == "MSH*abcd*\r", "Creating separate MSH");

        $msh->setField(1, "|");
        $msh->setField(2, "^~\\&");

        $this->assertTrue($msg->toString() == "MSH|^~\\&|\r", "Change MSH after add");

        $msh = new HL7\Segments\MSH(array());

        $msh->setField(1, "*");
        $msh->setField(2, "abcd");
        $msg->setSegment($msh, 0);

        $this->assertTrue($msg->toString() == "MSH*abcd*\r", "New MSH with setSegment");

        $str = 'MSH|^~\&|CodeRyte HL7|CodeRyte HQ|VISION|MISYS|200404061744||DFT^P03|TC-2743|P^T|2.3|||AL|NE||ASCII||| |';

        $msg = new HL7\Message($str);

        $this->assertTrue($msg->toString(1) == "$str\n", "Message from string and to string with subcomponents");

        // Segment as string
        $msg = new HL7\Message("MSH*^~\\&*1\rPID*a^b^c*a^b1&b2^c*xxx\r");
        $xxx = new HL7\Segment("XXX");
        $xxx->setField(2, array("a", array("b1", "b2"), "c"));

        $msg->addSegment($xxx);

        $this->assertTrue($msg->getSegmentAsString(0) == "MSH*^~\\&*1*", "MSH segment as string");

        $this->assertTrue($msg->getSegmentAsString(1) == "PID*a^b^c*a^b1&b2^c*xxx*", "PID segment as string");
        $this->assertTrue($msg->getSegmentAsString(2) == "XXX**a^b1&b2^c*", "XXX segment as string");

        // Get segment field as string
        $this->assertTrue($msg->getSegmentFieldAsString(0, 3) == "1", "MSH(3) as string");
        $this->assertTrue($msg->getSegmentFieldAsString(1, 2) == "a^b1&b2^c", "PID(2) as string");
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testBadSegment()
    {
        $seg5 = new HL7\Segment("ZZ1");
        $msg = new HL7\Message("MSH*.%#@*1\rPID***x.x@y@z.z\r");
        $msg->insertSegment($seg5);
    }
}