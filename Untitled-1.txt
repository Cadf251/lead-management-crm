QUAL É O MÍNIMO PARA CUMPRIR A V1?

- ✅ Receber leads.
- Distribuir leads.
- Vincular ofertas a equipes
- Equipe com função comercial
- Criar retornos
- Criar produtos
- Criar ofertas

O QUE DEIXAR PARA A V2?
- Recepção de interação
- Criação de jornadas
- Criação de perfil
- Cálculos de score
- Equipes com demais funções
- Ofertas com mais contexto

lead:
- id
- nome
- email
- celular
- score (opcional)
- array perfis
- array jornadas
- created

lead jornada:
- id
- status (pronto, em nutrição, descartado)
- lead_id
- oferta_id
- array interações

lead interação:
- id
- peso
- jornada_id
- tipo (pagina, email, artigo, vídeo)
- url
- utm
- tempo_engajamento

lead perfil:
- id
- lead_id
- status (rascunho, consolidado)
- dados

equipe:
- id
- nome
- descrição
- função (pré-venda, venda, pós-venda)
- status (ativo, pausado, cancelado)
- array ofertas

atendimento:
- id
- descrição
- primeiro contato
- status (ativo, arquivado)
- contato status
- jornada_id
- colaborador_id
- perfil_id
- array retornos

retorno:
- id
- atendimento_id
- meio (ligação, mensagem, visita, reunião)
- horário
- status (atendido, não atendido)

produto:
- id
- nome
- descrição

perfil alvo:
- id
- nome
- descricao
- dados

oferta:
- id
- nome
- tipo (comum, upsell, cross-sell)
- preco
- desconto
- data_inicio
- data fim
- status (ativo, pausado)
- produto_id
- perfil_alvo_id

equipe oferta:
- equipe_id
- oferta_id