<?php

namespace App\adms\Controllers\usuarios;

use App\adms\Helpers\GenerateLog;
use App\adms\Helpers\PHPMailerHelper;
use App\adms\Models\Repositories\TokenRepository;
use App\adms\Models\Repositories\UsuariosRepository;
use App\adms\Models\Services\DbConnectionClient;
use App\adms\Views\Services\LoadViewService;

/** 
 * Define funções universais para as classes de usuários. 
 * Tem o objetivo de ser herdado. 
 * Instancia o repositório automaticamente.
 */
abstract class UsuariosAbstract
{
  /** @var array $data Contém as informações da VIEW, evite usar no back-end. */
  protected array $data = [
    "title" => "Usuários",
    "css" => ["public/adms/css/usuarios.css"],
    "js" => []
  ];

  /** @var int $id O ID do usuário */
  public int $id = 0;

  /** @var string $nome O nome do usuário */
  public string $nome = "";

  /** @var string $email O e-mail do usuário */
  public string $email = "";

  /** @var int $statusId O ID do status do usuário. 1 = aguardando confirmação; 2 = Desativado; 3 = Ativado */
  public int $statusId;

  /** @var int $nivId O ID do nível de acesso */
  public int $nivId;

  /** @var protected $repo O repositório de usuários */
  protected $repo;

  /** @var protected $tokenRepo O repositório de TOKENs */
  protected $tokenRepo;

  /** Conecta com o banco de dados do cliente depois inicia o repositório de usuários e de tokens */
  public function __construct(array|null $credenciais = null)
  {
    $conn = new DbConnectionClient($credenciais);
    $this->repo = new UsuariosRepository($conn->conexao);
    $this->tokenRepo = new TokenRepository($conn->conexao);
  }

  /** 
   * Inclui no array $this->data valores adicionais que serão passados para o VIEW.
   */
  protected function setData(array $data): void
  {
    $this->data = array_merge($this->data, $data);
  }

  /** 
   * Retorna o $this->data.
   * 
   * @return array
   */
  protected function getData(): array
  {
    return $this->data;
  }

  /**
   * Instancia e carrega a view.
   * 
   * @param string $viewPath O caminho completo para a view
   */
  protected function render(string $viewPath): void
  {
    $loadView = new LoadViewService($viewPath, $this->getData());
    $loadView->loadView();
  }

  /**
   * Redireciona de volta para "listar usuário".
   */
  public function redirect(): void
  {
    header("Location: {$_ENV['HOST_BASE']}listar-usuarios");
    exit;
  }

  /** 
   * Recupera os dados do usuário e seta na classe. Também trata os erros e faz o direcionamento caso falhe.
   * 
   * @param int $usuarioId O ID do usuário.
   */
  public function setInfoById(int $usuarioId):void
  {
    $this->id = (int)$usuarioId;
    $usuarioArray = $this->repo->selecionar($this->id);

    // Verifica se houve erro
    if (($usuarioArray === false) || (empty($usuarioArray))) {
      GenerateLog::generateLog("error", "A consulta ao repositório retornou falso ou vazio", ["id" => $usuarioId]);

      // Prepara o setWarning
      $_SESSION["alerta"] = [
        "Erro!",
        "❌ O usuário não existe ou foi excluído."
      ];

      $this->redirect();
    }

    // Simplifica o array
    $usuario = $usuarioArray[0];

    // Seta os parâmetros do usuário
    $this->nome = $usuario["u_nome"];
    $this->email = $usuario["u_email"];
    $this->nivId = $usuario["niv_id"];
    $this->statusId = $usuario["us_id"];

    // Seta como array também para passar para a VIEW se necessário
    $this->data["usuario"] = $usuario;
  }

  /**
   * Armazena uma foto de perfil de um usuário. Não funciona se o $_FILES["foto"] não estiver setado.
   * 
   * Precisa dos parâmetros setados: id
   * 
   * @return bool Se o upload funcionou ou não
   */
  public function armazenarFoto(): bool
  {
    // Verifica se o id está setado
    if ($this->id === 0){
      GenerateLog::generateLog("error", "O ID do usuário não está setado.", ["metodo" => "armazenarFoto()"]);
      return false;
    }

    ini_set('file_uploads', '1');

    $extensoes_permitidas = [
      'image/jpeg' => '.jpg',
      'image/png'  => '.png',
      'image/jpeg' => '.jpeg'
    ];

    $tipo = mime_content_type($_FILES['foto']['tmp_name']);

    // Verifica se o tipo existe
    if (!array_key_exists($tipo, $extensoes_permitidas)) {
      GenerateLog::generateLog("error", "Formato de arquivo inválido na tentativa de upload para foto de perfil.", ["tipo" => $tipo, "usuario_id" => $this->id, "filearray" => $_FILES["foto"]]);
      return false;
    }

    $arquivoNome = $this->id . $extensoes_permitidas[$tipo];
    $caminho = "files/uploads/{$_SESSION['servidor_id']}/fotos-perfil/";

    // Tenta criar o diretório se não existir
    if (!is_dir($caminho)) {
      $novaPasta = mkdir($caminho, 0777, true);
      if (!$novaPasta) {
        GenerateLog::generateLog("error", "Não foi possível criar a pasta.", ["pasta" => $caminho, "tipo" => $tipo, "usuario_id" => $this->id, "filearray" => $_FILES["foto"]]);
        return false;
      }
    }

    $tmp = $_FILES["foto"]["tmp_name"];
    $imagem = getimagesize($tmp);

    // Verifica se é uma imagem
    if (!$imagem) {
      GenerateLog::generateLog("error", "O arquivo não é uma imagem.", ["tipo" => $tipo, "usuario_id" => $this->id, "filearray" => $_FILES["foto"]]);
      return false;
    }

    $final = $caminho . $arquivoNome;

    // Verifica se o arquivo existe
    if (file_exists($final)) {
      GenerateLog::generateLog("error", "O arquivo já existe. Fique atento a IDs duplicados.", ["tipo" => $tipo, "usuario_id" => $this->id, "filearray" => $_FILES["foto"]]);
      return false;
    }

    // Tenta criar os uploads
    if (move_uploaded_file($tmp, $final)) {
      // Se o usuário for o próprio, adiciona na SESSION
      if ($_SESSION["usuario_id"] === $this->id)
        $_SESSION["foto_perfil"] = $final;

      // Atualiza o banco de dados
      $tipoFormatado = str_replace(".", "", $extensoes_permitidas[$tipo]);
      $params = [
        ":foto_perfil" => $tipoFormatado
      ];
      $this->repo->updateUsuario($params, $this->id);
      return true;
    }

    GenerateLog::generateLog("error", "Não pôde armazenar por algum motivo.", ["tipo" => $tipo, "usuario_id" => $this->id, "filearray" => $_FILES["foto"], "caminho" => $caminho, "final" => $final]);
    return false;
  }

  /**
   * Apaga o arquivo da foto em uploads
   * 
   * Precisa setar: id
   * 
   * @return bool Se apagou ou não
   */
  public function apagarFoto(): bool
  {
    // Verifica se o id está setado
    if ($this->id === 0){
      GenerateLog::generateLog("error", "O ID do usuário não está setado.", ["metodo" => "apagarFoto()"]);
      return false;
    }

    $caminho = "files/uploads/{$_SESSION['servidor_id']}/fotos-perfil/{$this->id}";

    $extensoes_permitidas = ['.jpg', '.png', '.jpeg'];

    $ok = false;

    // Verica o arquivo para cada extensão
    foreach ($extensoes_permitidas as $tipo) {
      $arquivo = $caminho . $tipo;

      // Verifique se o arquivo existe
      if (!empty($arquivo) && file_exists($arquivo)) {
        unlink($arquivo);
        $ok = true;
      }
    }

    if ($ok === false)
      GenerateLog::generateLog("error", "A foto não foi apagada.", ["id" => $this->id]);
    else {
      // Atualiza o banco de dados
      $params = [
        ":foto_perfil" => null
      ];
      $this->repo->updateUsuario($params, $this->id);

      // Remove da $_SESSION caso seja o mesmo usuário
      if ($this->id === $_SESSION["usuario_id"])
        $_SESSION["foto_perfil"] = null;
    }

    return $ok;
  }

  /**
   * Troca a foto por uma nova, chamando a função apagarFoto e armazenarFoto
   * 
   * Precisa setar: id
   * 
   * @return bool Se funcionou ou não
   */
  public function trocarFoto(): bool
  {
    $apagar = $this->apagarFoto();

    if ($apagar === false)
      return false;

    return $this->armazenarFoto();
  }

  /**
   * Enviar um email de confirmação
   * 
   * Precisa dos parâmetros setados: id, nome, email
   * 
   * @return bool Se funcionou ou não
   */
  public function emailConfirmacao(): bool
  {
    // Verifica se os parâmetros estão devidamente setados
    if (($this->id === 0) || ($this->nome === "") || ($this->email === "")) {
      GenerateLog::generateLog(
        "error",
        "O método emailConfirmacao() foi chamado, mas os parâmetros do usuário não estavam setados corretamente.",
        ["id" => $this->id, "nome" => $this->nome, "email" => $this->nome]
      );
      return false;
    }

    // Cria devidamente o TOKEN

    // Prazo de 7 dias
    $prazo = date($_ENV['DATE_FORMAT'], strtotime('+7 days'));
    $token = $this->tokenRepo->armazenarToken("sistema", "confirmar_email_senha", $prazo, $this->id);

    // Cria o email de envio
    $mail = new PHPMailerHelper;

    $mail->destinatarios([$this->email]);

    $mail->imagens([["caminho" => "public/adms/img/logo.png", "nome" => "logo"]]);

    $params = [
      "[NOME]" => $this->nome,
      "[SERVIDOR_ID]" => $_SESSION["servidor_id"],
      "[TOKEN]" => $token
    ];

    $body = <<<HTML
    <html>
      <body style="background-color: #bbb; font-family: helvetica, arial;">
        <div style="margin-left:auto;margin-right:auto;margin-top: 30px;margin-bottom:30px;min-width: 300px; max-width:600px;background-color:#f0f0f0">
          <table style="border-collapse:collapse;">
            <tr>
            <td style="background-color: #404040; margin: 0 auto"><img src="cid:logo" alt="Logo RD Mind" width="70%" style="padding: 50px 0; margin: 0 15%"></td>
            </tr>
          </table>
          <table style="border-collapse:collapse">
            <tr>
              <td style="width:30px;height:30px"></td>
              <td></td>
              <td style="width:30px;height:30px"></td>
            </tr>
            <tr>
              <td style="width:30px;height:30px"></td>
              <td><p style="color: #222; font-size: 18px;">Olá [NOME], confirme o seu email acessando nosso portal pela primeira vez e criando uma senha.<br>Use o código da empresa: [SERVIDOR_ID]</p></td>
              <td style="width:30px;height:30px"></td>
            </tr>
            <tr>
              <td style="width:30px;height:30px"></td>
              <td></td>
              <td style="width:30px;height:30px"></td>
            </tr>
            <tr>
              <td style="width:30px;height:30px"></td>
              <td style="text-align: center;"><a style="padding: 15px 20px;font-size: 20px; color: #f0f0f0;font-weight: bold; background-color: #0ebcc9;" href="{$_ENV['HOST_BASE']}nova-senha/[SERVIDOR_ID]-[TOKEN]">Acessar</a></td>
              <td style="width:30px;height:30px"></td>
            </tr>
            <tr>
              <td style="width:30px;height:30px"></td>
              <td></td>
              <td style="width:30px;height:30px"></td>
            </tr>
            <tr>
              <td style="width:30px;height:30px"></td>
              <td></td>
              <td style="width:30px;height:30px"></td>
            </tr>
          </table>
          <table style="border-collapse: collapse; background-color: #404040; color: #f0f0f0;">
            <tr><td style="width:30px;height:30px"></td><td></td><td style="width:30px;height:30px"></td></tr> 
            <tr>
              <td style="width:30px;"></td>
              <td><p style="font-size: 24px; font-weight: bold; margin: 16px 0 0 0;">Entenda o universo do Marketing Digital com o nosso Blog</p></td>
              <td style="width:30px;"></td>
            </tr>
            <tr>
              <td style="width:30px;"></td>
              <td><a style="color: #f0f0f0; margin: 8px 0; font-size: 18px;" href="https://blog.agenciardmind.com.br/trafego-pago/o-que-e-trafego-pago.php">O que é Tráfego Pago?</a><br><a style="color: #f0f0f0; margin: 8px 0; font-size: 18px;" href="https://blog.agenciardmind.com.br/sites/como-criar-um-site.php">Como criar um site?</a></td>
              <td style="width:30px;"></td>
            </tr>
            <tr>
              <td style="width:30px;"></td>
              <td><p style="font-size: 24px; font-weight: bold; margin: 16px 0">✅ Responda esse email</p></td>
              <td style="width:30px;"></td>
            </tr>
            <tr><td style="width:30px;height:30px"></td><td></td><td style="width:30px;height:30px"></td></tr> 
          </table>
        </div>
      </body>
    </html>
    HTML;

    if ($body === false)
      return false;

    $body = $mail->parameters($body, $params);

    $title = "RD Mind | Confirme seu E-mail e Crie sua Senha";
    $mail->setarConteudo($title, $body);
    return $mail->enviar();
  }
}
