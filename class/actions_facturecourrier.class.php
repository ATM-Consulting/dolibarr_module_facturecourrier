<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) 2015 ATM Consulting <support@atm-consulting.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    class/actions_facturecourrier.class.php
 * \ingroup facturecourrier
 * \brief   This file is an example hook overload class file
 *          Put some comments here
 */

/**
 * Class ActionsFactureCourrier
 */
class ActionsFactureCourrier
{
	/**
	 * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = array();

	/**
	 * @var string String displayed by executeHook() immediately after return
	 */
	public $resprints;

	/**
	 * @var array Errors
	 */
	public $errors = array();

	/**
	 * Constructor
	 */
	public function __construct()
	{
	}

	/**
	 * Overloading the doActions function : replacing the parent's function with the one below
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    &$object        The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          &$action        Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	function doActions($parameters, &$object, &$action, $hookmanager)
	{
		
		if (in_array('invoicecard', explode(':', $parameters['context'])))
		{
	
				if($action == 'update_courrier') {
					global $user;
					
					$object->array_options['options_courrier_envoi'] = time();
					$object->update($user);
					
				}
				
	
		}
		
	}
	
	function getFormMail($parameters, &$object, &$action, $hookmanager)
	{
		
		if (in_array('formmail', explode(':', $parameters['context'])))
		{
			if(!empty($object->param['facid'])) {
				global $db;
				
				$facture = new Facture($db);
				$facture->fetch($object->param['facid']);
				
				if($facture->socid>0) {
					$societe=new Societe($db);
					$societe->fetch($facture->socid);
					
					if($societe->array_options['options_facture_papier'] == 1) {
						
						?><script type="text/javascript">
							$("<div>Attention ce client est paramétré par défaut en courrier</div>").dialog({
								modal:true
								,title:"Attention !"
								,buttons: {
						        	Ok: function() {
						          		$( this ).dialog( "close" );
						        	}
						      	}
							});
						</script><?php
						
					}
				
				}
				
			}						
	
		}
		
	}

	function formObjectOptions($parameters, &$object, &$action, $hookmanager)
	{
		
		if (in_array('invoicecard', explode(':', $parameters['context'])))
		{
			if($object->id>0 && $object->statut = 1 && !empty($object->array_options['options_courrier_envoi']) ) {
				
				?>
				<script type="text/javascript">
					$(document).ready(function() {
						$('div.tabsAction').first().append('<div class="inline-block divButAction"><a href="?facid=<?php echo $object->id ?>&action=update_courrier" class="butAction"><?php echo $langs->trans('ClassifyCourrier') ?></a></div>');
					});
				</script>
				<?php
				
			}
		}

		
	}
}