<?php

/**
 * Copyright 2010 http://rogeriolino.com
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace SegHEIRS\modules\integrations\hl7;

use SegHEIRS\modules\integrations\hl7\exceptions\SocketException;
use Yii;

/**
 * Socket
 *
 * @author rogeriolino
 */
class Socket {

    private $port     = 3000;
    private $address  = "localhost";
    private $blocking = true;

//    private $buffer   = 2048;
    private $buffer   = 1024;
    private $backlog  = 5;

    private $type     = SOCK_STREAM;
    private $family   = AF_INET;
    private $protocol = SOL_TCP;

    private $resource;

    /**
     * Socket constructor
     * @param  $resource
     */
    public function  __construct($resource = null) {
        if ($resource == null) {
            $this->resource = socket_create($this->family, $this->type, $this->protocol);
            if ($this->resource === false) {
                $errmsg = socket_strerror(socket_last_error());
                throw new SocketException($errmsg);
            }
        } else {
            $this->resource = $resource;
        }
    }

    /**
     * Returns the socket port
     * @return int
     */
    public function getPort() {
        return $this->port;
    }

    /**
     * Define the socket port
     * @param int $port
     */
    public function setPort($port) {
        $this->port = $port;
    }

    /**
     *
     * @return string
     */
    public function getAddress() {
        return $this->address;
    }

    public function setAddress($address) {
        $this->address = $address;
    }

    /**
     * Returns if the socket is blocking mode
     * @return boolean
     */
    public function isBlocking() {
        return $this->blocking;
    }

    /**
     * Defines whether the socket is blocking or nonblocking mode
     * @param boolean $blocking
     */
    public function setBlocking($blocking) {
        $this->blocking = ($blocking == true);
        $this->updateBlockingMode();
    }

    /**
     * Returns the socket communication type
     * @return int
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Define the socket communication type
     * @param int $type
     */
    public function setType($type) {
        if ($type == SOCK_STREAM || $type == SOCK_DGRAM || $type == SOCK_SEQPACKET || $type == SOCK_RAW || $type == SOCK_RDM) {
            $this->type = $type;
        } else {
            throw new SocketException("Invalid socket communication type");
        }
    }

    /**
     * Returns the socket protocol family
     * @return int
     */
    public function getFamily() {
        return $this->family;
    }

    /**
     * Define the socket protocol family
     * @param int $family
     */
    public function setFamily($family) {
        if ($family == AF_INET || $family == AF_INET6 || $family == AF_UNIX) {
            $this->family = $family;
        } else {
            throw new SocketException("Invalid socket protocol family");
        }
    }

    /**
     * Returns the socket protocol
     * @return int
     */
    public function getProtocol() {
        return $this->protocol;
    }

    /**
     * Define the socket protocol, must be compatible whit the protocol family
     * @param int $protocol
     */
    public function setProtocol($protocol) {
        if ($protocol == SOL_TCP || $protocol == SOL_UDP || $protocol == SOL_SOCKET) {
            $this->protocol = $protocol;
        } else {
            throw new SocketException("Invalid socket protocol");
        }
    }

    /**
     * Binds to a socket
     * @throws SocketException
     */
    public function bind() {
        if (socket_bind($this->resource, $this->address, $this->port) === false) {
            throw new SocketException("Socket bind failed: " . $this->error());
        }
    }

    /**
     * Listens for a connection on a socket
     * @throws SocketException
     */
    public function listen() {
        if (socket_listen($this->resource, $this->backlog) === false) {
            throw new SocketException("Socket listen failed: " . $this->error());
        }
    }

    /**
     * Accepts a connection
     * @throws SocketException
     * @return Socket
     */
    public function accept() {
        $sock = socket_accept($this->resource);
        if ($sock === false) {
            throw new SocketException("Socket accept failed: " . $this->error());
        }
        return new Socket($sock);
    }

    /**
     * Reads data from the connected socket
     * @return string
     */
    public function read() {
        $data = "";
        while (($m = socket_read($this->resource, $this->buffer)) != "") {
            $data .= $m;
        }
        return $data;
    }
    
    /**
     * Receives data from the connected socket
     * @return string
     */
    public function receive() {        
        try {
            $buffer = "";                 
            $this->setReceiveTimeOut();
            $bytes = socket_recv($this->resource, $buffer, $this->buffer, MSG_WAITALL);
            return ($buffer !== "" && $buffer !== null && $bytes !== false) ? $buffer : "";
        }
        catch (SocketException $e) {
            return "";
        }
    }
    
    private function setReceiveTimeOut() {        
        $module = Yii::app()->getModule('integrations')->getModule('jnj');        
        socket_set_option($this->resource, SOL_SOCKET, SO_RCVTIMEO, array("sec" => $module->receiveAckTimeOut, "usec" => 0));
    }

    /**
     * Write the data to connected socket
     * @param string $data
     */
    public function write($data) {
        socket_write($this->resource, $data, strlen($data));
    }

    /**
     * Initiates a connection on a socket
     * @throws SocketException
     * @return boolean
     */
    public function connect() {
        try {
            return socket_connect($this->resource, $this->address, $this->port);
        } catch (SocketException $e) {
            return false;
        }
    }

    /**
     * Close the socket connection
     */
    public function close() {
        socket_close($this->resource);
    }

    private function updateBlockingMode() {
        if ($this->blocking) {
            socket_set_block($this->resource);
        } else {
            socket_set_nonblock($this->resource);
        }
    }

    public function error() {
        return socket_strerror(socket_last_error($this->resource));
    }

}

