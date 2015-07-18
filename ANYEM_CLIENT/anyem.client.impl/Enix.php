<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Enix
 *
 * @author Amina
 */
class Enix extends Thread{
    
    public function simulate () {
        $this->start();
    }
    
    public function run() {
        print "Thread Started \n";
        sleep(5);
        print "Thread Terminated \n";
    }
}

$e = new Enix();
$e1 = new Enix();

$e->simulate();
sleep(1);
$e1->simulate();

$e1->join();
$e->join();
print "context terminated\n";