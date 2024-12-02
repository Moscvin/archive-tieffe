<template>
  	<div class="row">
    	<div class="col-md-6">
      		<div class="box box-success">
				<div class="box-header with-border">
					<i class="fa fa-calendar-alt"></i>
					<h3 class="box-title"> Lista degli interventi del da pianificare</h3>
				</div>
				<div class="box-body">
					<table class="table">
						<thead>
							<tr>
								<th>Cliente</th>
								<th>Località</th>
								<th>Tipologia</th>
								<th>Esito</th>
								<th></th>
							</tr>
						</thead>
						<tbody id="inteventi-list">
							<tr>
								<td colspan="5" class="title-day">Senza data</td>
							</tr>
							<tr v-for="(inter, i) in interventi" :key="i" :class="inter.className" :data-id="inter.id_intervento">
								<td>{{inter.client_name != null ? inter.client_name : ''}}</td>
								<td>{{inter.localita != null ? inter.localita : ''}}</td>
								<td>{{inter.tipologia != null ? inter.tipologia : ''}}</td>
								<td>{{inter.esito != null ? inter.esito : ''}}</td>

								<td>
									<a href="#" @click.prevent="openModal(inter)" title="Fissa l’intervento"><i class="fas fa-calendar"></i></a>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
      	</div>

		<div class="col-md-6">
      		<div class="box box-warning">
				<div class="box-header with-border">
					<i class="fa fa-calendar-alt text-warning"></i>
					<h3 class="box-title text-warning">Elementi che richiedono un intervento</h3>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-md-12" v-if="option.meccanica">
							<div class="small-box bg-gray">
								<div class="inner">
									<h3>{{option.meccanica}}</h3>
									<p>Interventi di oggi (meccanica)</p>
								</div>
								<div class="icon">
									<i class="fas fa-wrench"></i>
								</div>
								<a href="/monitoring" class="small-box-footer">Vai <i class="fas fa-arrow-circle-right"></i></a>
							</div>
						</div>
						<div class="col-md-12" v-if="option.verde">
							<div class="small-box bg-green">
								<div class="inner">
									<h3>{{option.verde}}</h3>
									<p>Interventi di oggi (verde)</p>
								</div>
								<div class="icon">
									<i class="fas fa-leaf"></i>
								</div>
								<a href="/monitoring" class="small-box-footer">Vai <i class="fas fa-arrow-circle-right"></i></a>
							</div>
						</div>
						<div class="col-md-12" v-if="option.pronto">
							<div class="small-box bg-orange">
								<div class="inner">
									<h3>{{option.pronto}}</h3>
									<p>Pronti interventi in attesa</p>
								</div>
								<div class="icon">
									<i class="fas fa-exclamation-triangle"></i>
								</div>
								<a href="/planning" class="small-box-footer">Vai <i class="fas fa-arrow-circle-right"></i></a>
							</div>
						</div>
						<div class="col-md-12" v-if="option.reports">
							<div class="small-box bg-blue">
								<div class="inner">
									<h3>{{option.reports}}</h3>
									<p>Rapporti da fatturare</p>
								</div>
								<div class="icon">
									<i class="fas fa-euro-sign"></i>
								</div>
								<a href="/lavori_da_fatturare" class="small-box-footer">Vai <i class="fas fa-arrow-circle-right"></i></a>
							</div>
						</div>
                    </div>
				</div>
			</div>
      	</div>
		<sweet-modal ref="edit" class="add-edit-modal">
			<div class="sweet-title full-color" :class="selectedIntervent.className">
				<h3>Programma l'intervento</h3>
			</div>
			<div class="content">
				<div class="d-flex2">
					<div class="form-group">
						<label for="">Cliente</label>
						<p>{{selectedIntervent.client_name != null ? selectedIntervent.client_name : ''}}</p>
					</div>
					<div class="form-group" :class="errors.has('tipo') ? 'has-error' : ''">
						<label for="tipo" :class="selectedIntervent.stato !=2 ? 'required' : ''">Tipologia</label>
						<select
						name="tipo"
						id="tipo"
						class="form-control w-100"
						v-model="selectedIntervent.tipo"
						data-vv-as="(Tipologia)"
						v-validate="selectedIntervent.stato !=2 ? 'required' : ''"
						@change="getTecnici(selectedIntervent.tipo)"
						:disabled="selectedIntervent.stato==2 ? true : false"
						key="tipo-input">
							<option value="1">Meccanica</option>
							<option value="2">Verde</option>
						</select>
					</div>
				</div>
				<div class="d-flex">
					<div class="form-group" :class="errors.has('descrizione_intervento') ? 'has-error' : ''">
						<label for="descrizione_intervento" :class="selectedIntervent.stato !=2 ? 'required' : ''">Descrizione</label>
						<input name="descrizione_intervento" id="descrizione_intervento" class="form-control w-100" v-model="selectedIntervent.descrizione_intervento"
							data-vv-as="(Descrizione)" v-validate="selectedIntervent.stato !=2 ? 'required' : ''" :disabled="selectedIntervent.stato==2 ? true : false"
							key="descrizione_intervento-input">
					</div>
					<div class="form-group" :class="errors.has('stato') ? 'has-error' : ''">
						<label for="stato" :class="selectedIntervent.stato !=2 ? 'required' : ''">Da programmare</label>
						<select name="stato" id="stato" class="form-control w-100" v-model="selectedIntervent.stato" data-vv-as="(Da programmare)"
							v-validate="selectedIntervent.stato !=2 ? 'required' : ''"
							:disabled="selectedIntervent.stato==2"
							key="stato-input">
							<option value="0">Si</option>
							<option value="1">No</option>
						</select>
					</div>
				</div>
				<div class="d-flex">
					<div class="form-group" :class="errors.has('data') ? 'has-error' : ''">
						<label :class="(selectedIntervent.stato==1  && selectedIntervent.stato !=2)? 'required': ''">Data</label>
						<el-date-picker v-model="selectedIntervent.data" name="data" type="date" placeholder="seleziona la data" format="dd/MM/yyyy" value-format="yyyy-MM-dd"
							class="w-100" data-vv-as="(Data)"
							v-validate="(selectedIntervent.stato==1  && selectedIntervent.stato !=2) ? 'required': ''"
							key="data-input" :disabled="(selectedIntervent.stato==0 || selectedIntervent.stato==2) ? true : false"
						>
						</el-date-picker>
					</div>
					<div class="form-group" :class="errors.has('ora') ? 'has-error' : ''">
						<label for="ora" :class="selectedIntervent.stato==1 ? 'required': ''">Ora</label>
						<el-time-select v-model="selectedIntervent.ora" name="ora" format="hh:mm" value-format="hh:mm"
							:picker-options="{
								start: '08:30',
								step: '00:15',
								end: '18:30'
							}"
							placeholder="seleziona la ora" class="w-100"
							:disabled="[0,2].includes(selectedIntervent.stato)" data-vv-as="(Data)"
							v-validate="selectedIntervent.stato==1 ? 'required': ''" key="ora-input">
						</el-time-select>
					</div>
				</div>

				<div class="d-flex">
					<div class="form-group" :class="errors.has('tecnico_gestione') ? 'has-error' : ''">
						<label for="tecnico_gestione" :class="selectedIntervent.stato == 1 ? 'required': ''">Tecnico responsabile</label>
						<select
							name="tecnico_gestione"
							id="tecnico_gestione"
							class="form-control w-100"
							v-model="selectedIntervent.tecnico_gestione"
							:disabled="selectedIntervent.stato==2"
							@change="setResponsabile"
							data-vv-as="(Tecnico)"
							v-validate="selectedIntervent.stato == 1 ? 'required': ''"
							key="tecnico_gestione-input">
							<option value=''>Seleziona tecnico</option>
							<option :value="tecnico.id_user" v-for="(tecnico, index) in tecnici" :key="index">{{tecnico.family_name+' '+tecnico.name}}</option>
						</select>
					</div>

					<div class="form-group" :class="errors.has('tecnici') ? 'has-error' : ''">
						<label for="tecnici" :class="selectedIntervent.stato!=2 ? 'required': ''">Tecnici</label>
						<multiselect
							v-model="selectedIntervent.tecnici_selected"
							:disabled="selectedIntervent.stato==2"
							:options="tecnici"
							:multiple="true"
							return="id_user"
							:close-on-select="false"
							:clear-on-select="false"
							:preserve-search="true"
							placeholder="Seleziona tecnici"
							select-label="Clica/Invio per selezione"
							selected-label="Selezionato"
							deselect-label="Clica/Invio per delezione"
							v-validate="selectedIntervent.stato!=2 ? 'required': ''"
							label="id_user"
							track-by="id_user"
							:custom-label="tecniciLabel"
						>
							<template slot-scope="{ values, search, isOpen }">
								<span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ values.length }} tecnici</span>
							</template>
						</multiselect>
					</div>
				</div>

				<div class="d-flex">
					<div class="form-group">
						<label for="fatturazione_status">Fatturazione Mensile</label>
						<select
						name="fatturazione_status"
						id="fatturazione_status"
						class="form-control w-100"
						v-model="selectedIntervent.fatturazione_status"
						:disabled="selectedIntervent.stato==2"
						data-vv-as="(Fatturazione Mensil)"
						key="fatturazione_status-input">
							<option value='0'>No</option>
							<option value='1'>Si</option>
						</select>
					</div>
					<div class="form-group">
						<label for="fatturazione_mensil">Per Conto di</label>
						<select
						name="fatturazione_mensil"
						id="fatturazione_mensil"
						class="form-control w-100"
						v-model="selectedIntervent.fatturazione_mensil"
						data-vv-as="(Per Conto di)"
						:disabled="selectedIntervent.stato==2"
						key="fatturazione_mensil-input">
							<option value='1'>Cliente</option>
							<option value='2'>Interporto</option>
							<option value='3'>Consorzio</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="pronto_intervento">Data creazione: {{$moment(selectedIntervent.created_at).format("YYYY/MM/DD HH:mm")}}</label>
				</div>
				<div class="d-flex">
					<div class="form-group" :class="errors.has('note') ? 'has-error' : ''">
						<label for="note">Note</label>
						<textarea name="note" id="note" class="form-control"
						v-model="selectedIntervent.note"
						v-validate="{regex: /[0-9a-zA-Z\_\-\/\(\)\ \.]$/}"
						:disabled="selectedIntervent.stato==2 ? true : false"
						key="note-input"></textarea>
					</div>
				</div>
			</div>
			<div class="sweet-buttons" v-if="selectedIntervent.stato!=2">
				<a v-if="selectedIntervent.reportLink" class="btn btn-success" :href="selectedIntervent.reportLink"><i class="fas fa-download"></i></a>
				<button  class="btn btn-primary btn_general" @click="updateInterventi()" i-id="0"><i class="fas fa-save"></i>&nbsp;&nbsp;Salva</button>
			</div>
		</sweet-modal>
	</div>
</template>

<script>
	import it from "vee-validate/dist/locale/it";
	import VeeValidate, { Validator } from "vee-validate";
	Vue.use(VeeValidate);

	import "element-ui/lib/theme-chalk/index.css";
	import { SweetModal, SweetModalTab } from "sweet-modal-vue";
	export default {
  		props: ["chars"],
		components: {
			SweetModal,
			SweetModalTab
		},
		data() {
			return {
				selectedIntervent: {},
				tecnici: [],
				interventi: [],
				interventToUpdate: {
					id_intervento: "",
					id_clienti: 0,
					descrizione_intervento: '',
					data: null,
					ora_dalle: null,
					ora_alle: null,
					tipologia: null,
					stato: 1,
					tecnico_gestione: "",
					tecnici_selected: [],
					note: "",
					tipo: 1,
					fatturazione_mensil: 0,
					fatturazione_status: 0,
				},
				option: {
					meccanica: 0,
					verde: 0,
					pronto: 0,
					reports: 0
				},
			};
		},
		methods: {
			arrayveryfi(arr, val) {
				return arr.includes(val);
			},
			tecniciLabel ({ family_name, name }) {
				return `${family_name}  ${name}`
			},
			setResponsabile(event) {
				if(event.target.value) {
					if(!this.selectedIntervent.tecnici_selected) {
						this.selectedIntervent.tecnici_selected = [];
					}
					var user = this.tecnici.filter(function(tehnic) {
						return tehnic.id_user == event.target.value;
					});
					var isPresent = this.selectedIntervent.tecnici_selected.filter(function(tehnic) {
						return tehnic.id_user == event.target.value;
					});
					if(user.length && !isPresent.length) {
						this.selectedIntervent.tecnici_selected.unshift(user[0]);
					}
				}
			},
			eventSelected(event) {
				this.openModal(event);
			},
			openModal(inter) {
				this.selectedIntervent = inter;
				this.getTecnici(this.selectedIntervent.tipo)
				$(".add-edit-modal").find(".sweet-buttons .btn_general").attr("i-id",inter.id_intervento);
				this.$refs.edit.open();
			},
			getTecnici(type) {
				type = type ? type : 1;
				axios.get("/interventi-get-tecnici/" + type).then(function(res) {
					this.tecnici = res.data.tecnici;
					if(this.selectedIntervent.tecnici_selected) {
						this.selectedIntervent.tecnici_selected = this.selectedIntervent.tecnici_selected.filter(tecnico => {
						let isPresent = this.tecnici.filter(newTecnico => {
							return newTecnico.id_user == tecnico.id_user;
						});
						return isPresent.length;
						});
					}
					if(this.selectedIntervent.tecnico_gestione) {
						let isPresent = this.tecnici.filter(tecnico => {
							return tecnico.id_user == this.selectedIntervent.tecnico_gestione;
						});
						if(!isPresent) {
							this.selectedIntervent.tecnico_gestione = '';
						}
					}
				}.bind(this));
			},
			updateInterventi() {
				this.$validator.validate().then(result => {
					if(result) {
						this.interventToUpdate.id_clienti = this.selectedIntervent.id_clienti;
						this.interventToUpdate.id_intervento = this.selectedIntervent.id_intervento;
						this.interventToUpdate.tipo = this.selectedIntervent.tipo;

						this.interventToUpdate.descrizione_intervento = this.selectedIntervent.descrizione_intervento;
						this.interventToUpdate.stato = this.selectedIntervent.stato;

						this.interventToUpdate.data = this.selectedIntervent.data;
						this.interventToUpdate.ora_dalle = this.selectedIntervent.ora_dalle;
						this.interventToUpdate.ora_alle = this.selectedIntervent.ora_alle;
						this.interventToUpdate.tipologia = this.selectedIntervent.tipologia;

						this.interventToUpdate.tecnico_gestione = this.selectedIntervent.tecnico_gestione;
						this.interventToUpdate.tecnici_selected = this.selectedIntervent.tecnici_selected;
						this.interventToUpdate.note = this.selectedIntervent.note;
						this.interventToUpdate.fatturazione_mensil = this.selectedIntervent.fatturazione_mensil;
						this.interventToUpdate.fatturazione_status = this.selectedIntervent.fatturazione_status;


						axios.post("/calendarioUpdadeIntervent", {
							params: {
								id_intervento: this.interventToUpdate.id_intervento,
								intervent: this.interventToUpdate
							}
						}).then(function(res) {
							if(res.data.statut == "Success") {
								this.$refs.edit.close();
								this.$notify({
									title: "",
									message: "Intervento è stato aggiornato",
									type: "success"
								});
							}
							this.getInterventiDaProgrammare();
							this.getOptions();
						}.bind(this));
					}
				});
			},
			getInterventiDaProgrammare() {
				axios.get("/getEventsDaProgramare").then(function(res) {
					this.interventi = res.data.interventi;
				}.bind(this));
			},
			getOptions() {
				axios.get("/getDashboardData").then(function(res) {
					this.option = res.data;
				}.bind(this));
			}
		},
		mounted() {
			this.getInterventiDaProgrammare();
			this.getTecnici(this.selectedIntervent.tipo);
			this.getOptions();
		}
	};
</script>

<style lang="scss">
	.table-bordered > thead > tr > th,
	.table-bordered > tbody > tr > th,
	.table-bordered > tfoot > tr > th,
	.table-bordered > thead > tr > td,
	.table-bordered > tbody > tr > td,
	.table-bordered > tfoot > tr > td {
		border: 1px solid #e2dfdf;
	}
	.table-bordered {
		.alert-info {
			background-color: #ddebed !important;
		}
	}

	.table > tbody > tr > td,
	.table > thead > tr > th {
		border: 1px solid #c7c6c6 !important;
	}

	.loading {
		height: 50px;
	}
	.title-day {
		font-size: 1.1em;
		text-transform: capitalize;
		font-weight: 800;
		padding-top: 24px !important;
	}
	.bold {
		font-weight: bold;
	}
	.add-edit-modal {
  		background: rgba(0, 0, 0, 0.3) !important;
		.sweet-modal {
			box-shadow: 0 2px 3px rgba(0, 0, 0, 0.125) !important;
			.sweet-box-actions {
				top: -1px !important;
				right: 9px !important;
				.sweet-action-close {
					width: 20px !important;
					height: 20px !important;
					color: #000 !important;
					text-shadow: 0 1px 0 #fff !important;
					opacity: 0.2 !important;
					svg {
						width: 16px !important;
						height: 21px !important;
					}
					&:hover {
						background: transparent !important;
						opacity: 0.5 !important;
					}
				}
			}
			.sweet-content {
				padding: 0 !important;
				.sweet-title {
					padding: 15px !important;
					border-bottom: 1px solid #f4f4f4;
					h3 {
						margin: 0 !important;
						line-height: 1.42857143 !important;
						// color: #ffffff !important;
					}
				}
			}
		}
	}

	section.v-cal-content {
		.v-cal-weekdays {
			max-height: 5rem !important;
			color: #5a5a5a !important;
			div {
				font-size: 16px;
			}
		}
		.v-cal-days {
			.v-cal-day__number {
				font-size: 14px !important;
			}
		}
	}
	.ml-15 {
		margin-left: 15px;
	}
</style>
