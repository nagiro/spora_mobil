<?php

abstract class GenericParser {
    protected $idMunicipi;
    protected $barri;
    protected $text;

    public function setMunicipi($municipi) {
        if(!is_numeric($municipi) || empty($municipi)) {
            throw new Exception('El municipi no és vàlid');
        }

        $this->idMunicipi = $municipi;
    }

    public function setBarri($barri) {
        if(!is_numeric($barri)) {
            throw new Exception('El barri no és una columna vàlida');
        }

        $this->barri = $barri;
    }

    public function setText($text) {
        if(!is_numeric($text)) {
            throw new Exception('El text no és una columna vàlida');
        }

        $this->text = $text;
    }

    abstract public function parse();
}

?>