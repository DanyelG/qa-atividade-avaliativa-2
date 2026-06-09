<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Pessoa;

class PessoaIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function deve_listar_todas_as_pessoas()
    {
        $response = $this->get('/pessoas');
        $response->assertStatus(200);
    }

    /** @test */
    public function deve_criar_uma_pessoa_com_sucesso()
    {
        $dadosPessoa = [
            'nome' => 'Danyel Teste',
            'cpf' => '12345678901',
            'telefone' => '32999999999',
            'email' => 'teste@email.com'
        ];

        $response = $this->post('/pessoas', $dadosPessoa);

        // Correção: Sistemas web Laravel normalmente redirecionam (302) após um cadastro
        $response->assertStatus(302);
        $this->assertDatabaseHas('pessoas', [
            'nome' => 'Danyel Teste',
            'cpf' => '12345678901'
        ]);
    }

    /** @test */
    public function deve_atualizar_uma_pessoa_com_sucesso()
    {
        // 1. Cria uma pessoa inicial no banco de dados usando o Factory
        $pessoa = Pessoa::factory()->create([
            'nome' => 'Nome Antigo',
            'email' => 'antigo@email.com'
        ]);

        // 2. Define os novos dados que queremos alterar
        $dadosAtualizados = [
            'nome' => 'Nome Atualizado Valadao',
            'cpf' => $pessoa->cpf, // Mantém o mesmo CPF
            'telefone' => '32888888888',
            'email' => 'novo@email.com'
        ];

        // 3. Envia a requisição PUT ou PATCH para a rota de atualização
        $response = $this->put("/pessoas/{$pessoa->id}", $dadosAtualizados);

        // 4. Validações de QA: Redireciona após salvar e os dados novos estão no banco
        $response->assertStatus(302);
        $this->assertDatabaseHas('pessoas', [
            'id' => $pessoa->id,
            'nome' => 'Nome Atualizado Valadao',
            'email' => 'novo@email.com'
        ]);
    }

    /** @test */
    public function deve_deletar_uma_pessoa_com_sucesso()
    {
        $pessoa = Pessoa::factory()->create();

        $response = $this->delete("/pessoas/{$pessoa->id}");

        // Correção: Redireciona após deletar com sucesso
        $response->assertStatus(302);
        $this->assertDatabaseMissing('pessoas', ['id' => $pessoa->id]);
    }
}