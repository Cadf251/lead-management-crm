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
- caráter (comum, promocional)
- data_inicio
- data fim
- status (ativo, pausado)
- produto_id
- perfil_alvo_id

equipe oferta:
- equipe_id
- oferta_id