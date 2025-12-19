<?php

class Pessoa
{
  public string $nome;

  public function __construct(string $nome)
  {
    $this->setNome($nome);
  }

  public function setNome(string $nome) {
    $this->nome = $nome;
  }

  public function showNome()
  {
    echo $this->nome."<br>";
  }
}

class Editar
{
  public function mostrarPessoa(Pessoa $pessoa)
  {
    $pessoaB = $this->editarPessoa($pessoa);
    $pessoa->showNome();
    $pessoaB->showNome();
  }

  public function editarPessoa(Pessoa $pessoa)
  {
    $pessoa->setNome("Emanuel");
    return $pessoa;
  }
}

$pessoa = new Pessoa("João");

$editar = new Editar();
$editar->editarPessoa($pessoa);
$editar->mostrarPessoa($pessoa);