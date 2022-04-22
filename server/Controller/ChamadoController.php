<?php
class ChamadoController
{
  private $db;
  private $requestMethod;
  private $IdChamado;

  public function __construct($db, $requestMethod, $IdChamado)
  {
    $this->db = $db;
    $this->requestMethod = $requestMethod;
    $this->IdChamado = $IdChamado;
  }

  public function processRequest()
  {
    switch ($this->requestMethod) {
      case 'GET':
        if ($this->IdChamado) {
          $response = $this->getChamado($this->IdChamado);
        } else {
          $response = $this->getAllChamados();
        };
        break;
      case 'POST':
        $response = $this->createChamado();
        break;
      case 'PUT':
        $response = $this->updateChamado($this->IdChamado);
        break;
      case 'DELETE':
        $response = $this->deleteChamado($this->IdChamado);
        break;
      default:
        $response = $this->notFoundResponse();
        break;
    }
    header($response['status_code_header']);
    header_remove('Set-Cookie');
    if ($response['body']) {
      echo $response['body'];
    }
  }

  private function getAllChamados()
  {
    $query = "
      SELECT
          id, titulo, descricao, status, data_abertura, solicitante
      FROM
          chamados;
    ";

    try {
      $statement = $this->db->query($query);
      $result = $statement->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
      exit($e->getMessage());
    }

    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode($result);
    return $response;
  }

  private function getChamado($id)
  {
    $result = $this->find($id);
    if (!$result) {
      return $this->notFoundResponse();
    }
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode($result);
    return $response;
  }

  private function createChamado()
  {
    $sanitizeInput = $this->sanitizeInput(json_decode(
      file_get_contents('php://input'),
      TRUE
    ));
    $input = json_decode(json_encode($sanitizeInput), FALSE);
    if (!$this->validateInput($input)) {
      return $this->unprocessableEntityResponse();
    }

    $data = str_replace("/", "-", $input->data_abertura);
    $input->data_abertura = date('Y-m-d H:i:s', strtotime($data));

    $query = "
      INSERT INTO chamados
          (titulo, descricao, status, data_abertura, solicitante)
      VALUES
          (:titulo, :descricao, :status, :data_abertura, :solicitante);
    ";

    try {
      $statement = $this->db->prepare($query);
      $statement->execute([
        'titulo' => $input->titulo,
        'descricao'  => $input->descricao,
        'status' => $input->status,
        'data_abertura' => $input->data_abertura,
        'solicitante' => $input->solicitante,
      ]);
      $statement->rowCount();
    } catch (PDOException $e) {
      exit($e->getMessage());
    }

    $response['status_code_header'] = 'HTTP/1.1 201 Created';
    $response['body'] = json_encode(['message' => 'Chamado Aberto!']);
    return $response;
  }

  private function updateChamado($id)
  {
    $result = $this->find($id);
    if (!$result) {
      return $this->notFoundResponse();
    }
    $sanitizeInput = $this->sanitizeInput(json_decode(
      file_get_contents('php://input'),
      TRUE
    ));
    $input = json_decode(json_encode($sanitizeInput), FALSE);
    if (!$this->validateInput($input)) {
      return $this->unprocessableEntityResponse();
    }

    $statement = "
      UPDATE chamados
      SET
        titulo = :titulo,
        descricao  = :descricao,
        status = :status,
        data_abertura = :data_abertura,
        solicitante = :solicitante
      WHERE id = :id;
    ";

    $data = str_replace("/", "-", $input->data_abertura);
    $input->data_abertura = date('Y-m-d H:i:s', strtotime($data));

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute([
        'id' => (int) $id,
        'titulo' => $input->titulo,
        'descricao'  => $input->descricao,
        'status' => $input->status,
        'data_abertura' => $input->data_abertura,
        'solicitante' => $input->solicitante,
      ]);
      $statement->rowCount();
    } catch (PDOException $e) {
      exit($e->getMessage());
    }
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode(['message' => 'Chamado Atualizado!']);
    return $response;
  }

  private function deleteChamado($id)
  {
    $result = $this->find($id);
    if (!$result) {
      return $this->notFoundResponse();
    }

    $query = "
      DELETE FROM chamados
      WHERE id = :id;
    ";

    try {
      $statement = $this->db->prepare($query);
      $statement->execute(['id' => $id]);
      $statement->rowCount();
    } catch (PDOException $e) {
      exit($e->getMessage());
    }
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode(['message' => 'Chamado excluído!']);
    return $response;
  }

  public function find($id)
  {
    $query = "
      SELECT
          id, titulo, descricao, status, data_abertura, solicitante
      FROM
          chamados
      WHERE id = :id;
    ";

    try {
      $statement = $this->db->prepare($query);
      $statement->execute(['id' => $id]);
      $result = $statement->fetch(PDO::FETCH_OBJ);
      return $result;
    } catch (PDOException $e) {
      exit($e->getMessage());
    }
  }

  private function validateInput($input)
  {
    if (
      !isset($input->titulo) || !isset($input->descricao) ||
      !isset($input->status) || !isset($input->data_abertura) ||
      !isset($input->solicitante)
    ) {
      return false;
    }
    return true;
  }

  private function sanitizeInput($input)
  {
    if (is_int($input)) {
      return $input;
    }

    if (is_array($input)) {
      foreach ($input as $in => $put) {
        $input[$in] = $this->sanitizeInput($put);
      }
      return $input;
    }

    $input = trim($input);
    $input = stripslashes($input);
    return $input;
  }

  private function unprocessableEntityResponse()
  {
    $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
    $response['body'] = json_encode([
      'error' => 'Entrada inválida'
    ]);
    return $response;
  }

  private function notFoundResponse()
  {
    $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
    $response['body'] = null;
    return $response;
  }
}
