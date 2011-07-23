<!DOCTYPE html>
<html>
<head>
	<meta name="description" content="Bloco de Notas Online">
	<meta name="keywords" content="Bloco de Notas, Notepad, padeNote, Wordpad">
	<meta name="author" content="diegosilva.com.br">
	<meta charset="utf-8">

	<title>WordPad Online - Alpha</title>

	<link rel="stylesheet" href="<?=base_url();?>css/reset.css"	type="text/css" media="all">
	<link rel="stylesheet" href="<?=base_url();?>css/site.css"	type="text/css" media="all">
	<link rel="stylesheet" href="<?=base_url();?>css/jquery.loadmask.css" type="text/css" media="all" />
	
	<link rel="stylesheet" href="<?=base_url();?>css/smoothness/jquery-ui-1.8.9.custom.css"	type="text/css" media="all" />
	<link rel="stylesheet" href="<?=base_url();?>css/validationEngine.jquery.css" type="text/css" media="all" />
	<link rel="stylesheet" href="<?=base_url();?>css/jquery.noticeMsg.css" type="text/css" media="all" />
	<link rel="stylesheet" href="<?=base_url();?>css/jquery.alerts.css"	type="text/css" media="all" />
	<link rel="stylesheet" href="<?=base_url();?>css/jHtmlArea.css"	type="text/css" media="all" />
	
	<script type="text/javascript" src="<?=base_url();?>js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="<?=base_url();?>js/cufon-yui.js"></script>
	<script type="text/javascript" src="<?=base_url();?>js/cufon-replace.js"></script>
	<script type="text/javascript" src="<?=base_url();?>js/Myriad_Pro_300.font.js"></script>
	<script type="text/javascript" src="<?=base_url();?>js/Myriad_Pro_400.font.js"></script>
	
	<script type="text/javascript" src="<?=base_url();?>js/jquery-ui-1.8.1.custom.js"></script>
	<script type="text/javascript" src="<?=base_url();?>js/jquery.validationEngine.js"></script>
	<script type="text/javascript" src="<?=base_url();?>js/jquery.validationEngine-pt.js"></script>
	<script type="text/javascript" src="<?=base_url();?>js/jquery.form.js"></script>
	<script type="text/javascript" src="<?=base_url();?>js/jquery.loadmask.js"></script>
	<script type="text/javascript" src="<?=base_url();?>js/jquery.noticeMsg.js"></script>
	<script type="text/javascript" src="<?=base_url();?>js/jquery.alerts.js"></script>
	<script type="text/javascript" src="<?=base_url();?>js/jquery.scrollabletab.js"></script>
	<script type="text/javascript" src="<?=base_url();?>js/jHtmlArea-0.7.0.js"></script>
	
	<script type="text/javascript">

			//indica o indice da tab selecionada
			var indTabSel = 0;
			//armazena a url da aplicação em formato js
			urlApp = '<?= base_url();?>';
			//representa o objeto tabs da página
			var $abas;

			//funcao executada no onload da pagina
			$(function() {
				//adicionando componente de abas.
				$abas = $("#abas").tabs({
						tabTemplate: "<li><a href='#{href}' ondblclick='editTitle(this);'>#{label}</a><input onblur='endTitleEdit(this);' size='20' class='input_aba'/><span style='display: inline-block;' class='ui-icon ui-icon-close' onclick='excluirAba(event, this);'></span></li>",
						 select: function(event, ui) {
							 		indTabSel = ui.index;
						 }
					}).scrollabletab();

				$( "#btnAdicionar" ).button({
					text: false,
					icons: {
						primary: "icone-adicionar"
					}
				});

				$( "#btnSair" ).button({
					text: false,
					icons: {
						primary: "icone-logout"
					}
				});
				$( "#btnSalvar" ).button({
					text: false,
					icons: {
						primary: "icone-save"
					}
				});
				$( "#btnSalvarTodos" ).button({
					text: false,
					icons: {
						primary: "icone-save-all"
					}
				});


				//adicionando o dialogo de cadastro de usuarios
				$("#cad_usuario_div").dialog({
					autoOpen: false,
					height: 215,
					width: 214,
					modal: true,
					buttons: {
						'Cadastrar': function() {
								if($('#cad_usu_form').validationEngine('validate')){
									$('#cad_usu_form').validationEngine('hideAll');
									var queryString = $('#cad_usu_form').formSerialize();
									disableButtons();
									$("#cad_usuario_div").mask("Cadastrando, aguarde...");
									$.post(urlApp+'usuario/cadastrarUsuario', queryString, cadastrarApos, "json");
								}
							},
						'Cancelar': function() {
							$(this).dialog('close');
						}
					},
					beforeclose : function(){
						if($("#cad_usuario_div").isMasked()){
							return false;
						}else{
							$('#cad_usu_form').validationEngine('hideAll');
							return true;
						}
					}
				});

				//adicionando o dialogo de login
				$("#login_div").dialog({
					autoOpen: false,
					height: 210,
					width: 215,
					modal: true,
					buttons: {
						'Entrar': function() {
							if($('#login_form').validationEngine('validate')){
								$('#login_form').validationEngine('hideAll');
								var queryString = $('#login_form').formSerialize();
								disableButtons();
								$("#login_div").mask("Entrando, aguarde...");
								$.post(urlApp+'usuario/login', queryString, entrarApos, "json");
							}
						},
						'Cadastrar-se': function() {
							$(this).dialog('close');
							$('#cad_usuario_div').dialog('open');
						}
					},
					beforeclose : function(){
						jQuery('#login_form').validationEngine('hideAll');
						return true;
					}
				});
				$("#login_form").validationEngine();
				$("#cad_usu_form").validationEngine();

				executeIfUserNotLogged(escondeToolBar);
				executeIfUserLogged(recuperarAbasUsuario, exibeToolBar, exibeNomeUsuario);
			});

			//Funcao que esconde os botoes de sair e salvar
			function escondeToolBar(){
				$("#app-toolbar").hide();
				$("#liFotoLogado").hide();
				$("#liNomeLogado").hide();
				$("#liCadastrar").show();
				$("#liEntrar").show();
			}

			//Exibe os botões Sair e Salvar
			function exibeToolBar(){
				$("#app-toolbar").show();
				$("#liFotoLogado").show();
				$("#liNomeLogado").show();
				$("#liCadastrar").hide();
				$("#liEntrar").hide();
			}
			
			//acao executada após o metodo de login
			function entrarApos(data, result){
				try{
					if(data.success == true){
						$("#login_div").dialog('close');
						$('#login_form').clearForm(); 
						recuperarAbasUsuario();
						exibeToolBar();
						exibeNomeUsuario();
					}else{
						jAlert('Usu&aacute;rio ou senha inv&aacute;lidos!');
					}
					$("#login_div").unmask();
					enableButtons();
				}catch(e){
					jAlert('Ocorreu um erro no sistema, entre em contato com o site: '+e);
				}
			}
			//acao executada após o método de cadastro
			function cadastrarApos(data, result){
				try{
					if(data.success == true){
						$("#cad_usuario_div").unmask();
						$("#cad_usu_form").clearForm();
						$("#cad_usuario_div").dialog('close');
						jAlert('Usu&aacute;rio cadastrado com sucesso!', 'Aviso');
					}else{
						$("#cad_usuario_div").unmask();
						jAlert('Ocorreu um erro ao efetuar seu cadastro '+ data.msg);
					}
					enableButtons();
				}catch(e){
					jAlert('Ocorreu um erro no sistema, entre em contato com o site: '+e);
				}
			}

			//funcao para inicio de edicao do titulo
			function editTitle(obj){
				input = $("input",$(obj).parent());
				$(obj).hide();
				input.val($(obj).text());
				input.show();
				input.focus();
				input.select();
			}

			//edita o titulo da aba
			function endTitleEdit(obj){
				input =$(obj);
				span = $("a",input.parent());
				span.text(input.val());
				input.hide();
				span.show();
			}

			//adiciona uma aba com uma anotacao
			function adicionarAnotacao(titulo, texto, codigo){
				titulo = !titulo ? "Clique duas vezes aqui para renomear" : titulo;
				texto = !texto ? "" : texto;
				codigo = !codigo ? "":codigo;

				pos = $('#abas-ul li').size()-1;
				
				$("#abas").append("<div id='ant-"+pos+"'><textarea id='area-ant-"+pos+"' cols='180' rows='25' style='width:100%; height:70%;'>"+texto+"</textarea></div>");
				$("#abas").append("<input type='hidden' id='cod-ano-"+pos+"' value='"+codigo+"'/>");
				
				$abas.tabs("add","#ant-"+pos+"",titulo, pos);

				$abas.tabs("select", pos);

				$("textarea").htmlarea();
			}

			//Exclui uma aba do banco e da tela
			function excluirAba(event, obj, indice){
				$('a',obj.parentNode).click();

				if(!indice){
					indice = indTabSel;
				}

				if(indice != 0){
					codigo = $("#cod-ano-"+indice).val();
					if(codigo != ''){
						$('#divHeader').noticeMsg('Excluindo aba...', {dur: false});
						$.post(urlApp+'nota/delete', {id:codigo}, function(data, result){
							if(result === 'success'){
								$abas.tabs( "remove", indice);
								$abas.tabs("select", indice-1);
								$("#cod-ano-"+indice).remove();
								$('#divHeader').noticeMsg('Aba excluida com sucesso...');
							}else{
								$('#divHeader').noticeMsg('Aba não excluida com sucesso...');
							}
						}, "json");
					}else{
						$abas.tabs( "remove", indice);
						$abas.tabs("select", indice-1);
						$("#cod-ano-"+indice).remove();
					}
				}
			}
			
			//esconde os botoes
			function disableButtons(){
				$('.ui-button').addClass('ui-state-disabled');
				$('.ui-button').attr('disabled',true);
			}

			//Exibe os botoes
			function enableButtons(){
				$('.ui-button').removeClass('ui-state-disabled');
				$('.ui-button').attr('disabled', false);
			}

			//executa determinada acao somente se o usuario estiver logado
			function executeIfUserLogged(){
				execucoesLogado = arguments;
				$.post(urlApp+'usuario/isUserLogged', null, function(data, result){
					if(data.success){
						$(execucoesLogado).each(function(i, f){
							f.call();
						});
					}
				}, "json");
			}

			//executa determinada funcao somente se o usuario não estiver logado.
			function executeIfUserNotLogged(){
				execucoesNaoLogado = arguments;
				$.post(urlApp+'usuario/isUserLogged', null, function(data, result){
					if(!data.success){
						$(execucoesNaoLogado).each(function(i, f){
							f.call();
						});
					}
				}, "json");
			}

			//Recupera as abas do usuário logado.
			function recuperarAbasUsuario(){
				$.post(urlApp+'nota/getNotasUsuario', null, function(data, result){
					$(data).each(function(){
						adicionarAnotacao(this.titulo, this.texto, this.id);
						});
				}, "json");
			}

			//salva o documento da aba selecionada
			function salvarDocumento(){
				tit = $('.ui-tabs-selected a').text();
				txt = $("#area-ant-"+indTabSel).htmlarea("toHtmlString");
				codAno = $("#cod-ano-"+indTabSel).val();
				
				$('#divHeader').noticeMsg('Gravando texto '+tit+' ...', {dur: false});
				$.post(urlApp+'nota/salvar', {id:codAno, titulo:tit, texto:txt}, function(data, result){
					if(data.success){
						$("#cod-ano-"+indTabSel).val(data.id);
						$('#divHeader').noticeMsg('Concluido com sucesso!');
					}else{
						$('#divHeader').noticeMsg('Concluido com erro, tente novamente!');
					}
				}, "json");

			}

			//salva todos os documentos
			function salvarDocumentos(indice){
				var i = indice ? indice : 1;

				if(i < $('#abas-ul li').size()){
					var titulo = $('a',$('#abas-ul li').eq(i)).text();
					var codigo = $('#cod-ano-'+(i-1)).val();
					var texto = $('#area-ant-'+(i-1)).htmlarea("toHtmlString");

					$('#divHeader').noticeMsg('Gravando texto '+titulo+' ...', {dur: false});

					$.post(urlApp+'nota/salvar', {id:codigo, titulo:titulo, texto:texto}, function(data, result){
						if(data.success){
							 $('#cod-ano-'+(i-1)).val(data.id);
						}else{
							$('#divHeader').noticeMsg('Erro ao gravar!!!');
						}
						salvarDocumentos(i+1);
					}, "json");
				}else{
					$('#divHeader').noticeMsg('Concluido!!!');
				}
			}

			//Exibe o nome do usuario logado no lugar apropriado.
			function exibeNomeUsuario(){
				$.post(urlApp+'usuario/getDadosUsuarioJson', null, function(data, result){
					$("#spanUserName").text(data.nome);
					Cufon.replace('#spanUserName', { fontFamily: 'Myriad Pro Regular', color: '-linear-gradient(#fff, #cdcdcd)' });
				}, "json");
			}

			function logout(){
				$.post(urlApp+'usuario/logout', null, function(data, result){
					if(data.success === true){
						window.location.reload();
					}
				}, "json");
			}

//			$("#area-ant-0").val('<br><br>Teste Diego');
	//		$("#area-ant-0").htmlarea("updateHtmlArea");
		//	$("#area-ant-0").htmlarea("toHtmlString");
			
		</script>

	<!--[if lt IE 7]>
			<script type="text/javascript" src="http://info.template-help.com/files/ie6_warning/ie6_script_other.js"></script>
		<![endif]-->
	<!--[if lt IE 9]>
			<script type="text/javascript" src="./js/html5.js"></script>
		<![endif]-->
	</head>

<body>
	<header>
		<div class="container" id="divHeader">
			<div class="header-box">
				<div class="left">
					<div class="right">
						<nav>
							<ul>
								<li id="liCadastrar"><a
									onclick="$('#cad_usuario_div').dialog('open');" href="#">Cadastrar-se</a>
								</li>
								<li id="liEntrar"><a onclick="$('#login_div').dialog('open');"
									href="#">Entrar</a></li>

								<li id="liFotoLogado">
									<div id="usuario-ico"
										class="icone-usuario ui-corner-all border"
										style="width: 46px; height: 46px;"></div>
								</li>
								<li id="liNomeLogado"><span id="spanUserName"></span>
								</li>
							</ul>
						</nav>
						<h1>
							<a href="#"><span>Word</span>Pad</a>
						</h1>
					</div>
				</div>
			</div>
		</div>
	</header>

	<div id="app-toolbar" class="ui-widget-header ui-corner-all"
		style="margin: 5px auto; width: 89%; height: 25px;">
		<button id="btnAdicionar" style="height: 25px;"	onclick="adicionarAnotacao();">Adicionar Anotação</button>
		<button id="btnSalvar" style="height: 25px;" onclick="salvarDocumento();">Salvar Documento Atual</button>
		<button id="btnSalvarTodos" style="height: 25px;" onclick="salvarDocumentos();">Salvar Todos Documentos</button>
		<button id="btnSair" style="height: 25px; float: right;" onclick="logout();">Sair da Aplicação</button>
	</div>

	<div class="container">
		<div id="abas">
			<ul id="abas-ul" style="height: 22px;">
				<li></li>
			</ul>
		</div>
	</div>

	<footer>
		<div class="container" id="divFooter">
			<div class="footer-box">
				<div class="left">
					<div class="right">
						<a href="http://www.diegosilva.com.br"><span class="span1">Criado
								por</span> <span class="span2">DiegoSilva</span> </a>
					</div>
				</div>
			</div>
		</div>
	</footer>


	<div id="cad_usuario_div" title="Cadastrar-se">
		<form id="cad_usu_form">
			<label for="nome">Nome</label> <input type="text" name="nome"
				id="nome"
				class="input_form text ui-widget-content validate[required]" /> <label
				for="email">Email</label> <input type="text" name="email" id="email"
				class="input_form validate[required,custom[email],ajax[ajaxEmail]] text ui-widget-content  " />

			<label for="senha">Senha</label> <input type="password" name="senha"
				id="senha"
				class="input_form text ui-widget-content validate[required,length[8,50]]" />
		</form>
	</div>

	<div id="login_div" title="Entrar">
		<form id="login_form">
			<label for="login">Email</label> <input type="text" name="email"
				id="email"
				class="input_form text ui-widget-content validate[required,custom[email]]" />

			<label for="senha">Senha</label> <input type="password" name="senha"
				id="senha"
				class="input_form text ui-widget-content validate[required,length[8,50]]" />
			<p style="padding-top: 10px;">
				<input type="checkbox" name="manterConectado" id="manterConectado" />
				<label for="manterConectado">Permanecer conetado?</label>
			</p>
		</form>
	</div>
</body>
</html>
