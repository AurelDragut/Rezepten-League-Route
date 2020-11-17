<?php

namespace App\Classes;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface Controllable
{
    public function index(ServerRequestInterface $request) : ResponseInterface;

    public function display(ServerRequestInterface $request) : ResponseInterface;

    public function read(ServerRequestInterface $request) : ResponseInterface;

    public function create(ServerRequestInterface $request) : ResponseInterface;

    public function formFields(): array;

    public function edit(ServerRequestInterface $request) : ResponseInterface;

    public function save(ServerRequestInterface $request) : ResponseInterface;

    public function update(ServerRequestInterface $request) : ResponseInterface;

    public function delete(ServerRequestInterface $request) : ResponseInterface;
}
