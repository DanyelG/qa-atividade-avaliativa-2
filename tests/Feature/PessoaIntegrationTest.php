<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Pessoa;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PessoaIntegrationTest extends TestCase
{
    // Limpa o banco de dados temporário a cada teste para não dar conflito
    use RefreshDatabase;

    /** @test */
    public function deve_retornar_lista_de_pessoas_com_sucesso()
    {
        // Acessa a rota de listagem e garante que a tela abre (Status 200 OK)
        $response = $this->get(route('pessoas.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function deve_cadastrar_uma_nova_pessoa_no_banco_de_dados()
    {
        // 1. Dados falsos de uma pessoa para simular o preenchimento de um formulário
        $dadosPessoa = [
            'nome' => 'Ricardo Valadão',
            'email' => 'ricardo@teste.com',
            // Se o professor exigir mais campos no banco (como CPF ou telefone), eles entram aqui
        ];

        // 2. Simula o envio (POST) desses dados para a rota que salva no banco
        $response = $this->post(route('pessoas.store'), $dadosPessoa);

        // 3. Verifica se a pessoa realmente foi salva no banco de dados
        $this->assertDatabaseHas('pessoas', [
            'email' => 'ricardo@teste.com'
        ]);

        // 4. Geralmente o Laravel redireciona o usuário após salvar (Status 302)
        $response->assertStatus(302);
    }

    /** @test */
    public function deve_excluir_uma_pessoa_com_sucesso()
    {
        // 1. Cria uma pessoa direto no banco usando a Factory do Laravel para podermos deletar
        $pessoa = Pessoa::factory()->create();

        // 2. Envia uma requisição de DELETE para a rota de exclusão informando o ID
        $response = $this->delete(route('pessoas.destroy', $pessoa->id));

        // 3. Garante que o registro sumiu do banco de dados
        $this->assertDatabaseMissing('pessoas', [
            'id' => $pessoa->id
        ]);

        // 4. Verifica o redirecionamento após a exclusão
        $response->assertStatus(302);
    }
}