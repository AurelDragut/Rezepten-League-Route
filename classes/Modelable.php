<?php


namespace App\Classes;


interface Modelable
{
    public function rules($method): array;

    public function fill($data);

    public function save();

    public function update();

    public function delete();
}
