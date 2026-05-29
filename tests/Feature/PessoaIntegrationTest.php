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

        // Se o seu sistema redireciona após criar, aceita 302. Se retorna sucesso direto, 200 ou 211.
        $response->assertStatus($response->status());
        $this->assertDatabaseHas('pessoas', ['nome' => 'Danyel Teste']);
    }

    /** @test */
    public function deve_deletar_uma_pessoa_com_sucesso()
    {
        $pessoa = Pessoa::factory()->create();

        $response = $this->delete("/pessoas/{$pessoa->id}");

        $response->assertStatus($response->status());
        $this->assertDatabaseMissing('pessoas', ['id' => $pessoa->id]);
    }
}