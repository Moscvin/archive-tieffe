<template>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3>Nuovo intervento</h3>
                    <div class="box-body" v-if="success">
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                            <h4><i class="icon fa fa-check"></i> {{ success }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-fw fa-calendar-plus"></i>
                        <strong>Intervento</strong>
                    </h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                          <div class="row">
                            <div class="col-md-3">
                              <div class="form-group" :class="errors.has('tipologia') ? 'has-error' : ''">
                								<label class="required">Tipologia</label>
                								<select
                                  class="form-control w-100"
                                  v-model="operation.tipologia"
                                  v-validate="'required'"
                                  data-vv-as="(Tipologia)"
                                  @change="getMachineries"
                                >
                									<option value=''></option>
                                  <option value='Caldo'>Caldo</option>
                									<option value='Freddo'>Freddo</option>
                									<option value='Sopralluogo Caldo'>Sopralluogo Caldo</option>
                									<option value='Sopralluogo Freddo'>Sopralluogo Freddo</option>
                								</select>
                							</div>
                            </div>
                            <div class="col-md-3">
                              <div class="form-group" :class="errors.has('data') ? 'has-error' : ''">
                								<label :class="operation.status == 1 ? 'required': ''">Data</label>
                								<el-date-picker
                										v-model="operation.date"
                										name="data"
                										type="date"
                										placeholder="seleziona la data"
                										format="dd/MM/yyyy"
                										value-format="yyyy-MM-dd"
                										class="w-100"
                										@change="getOperationsByDate"
                										v-validate="operation.status == 1 ? 'required': ''"
                										data-vv-as="(Data)"
                										key="data-input"
                										:disabled="!operation.status"
                										:picker-options="pickerOptions"
                								>
                								</el-date-picker>
                							</div>
                            </div>
                            <div class="col-md-3">
                              <div class="form-group"> <!--  :class="errors.has('ora_dalle') ? 'has-error' : ''" -->
                                <label class="d-block w-100" :class="operation.status == 1 ? 'required': ''">Ora dalle</label>
                                <date-picker
                                  tabindex="1"
	                                v-model="operation.ora_dalle"
	                                name="ora_dalle"
	                                placeholder="dalle"
                                    data-vv-as="(Dalle)"
                                    key="data-input"
                                    v-validate="{ required: (operation.status == 1 ? true : false), operationStartHour:true }"
	                                :config="btTimeOptions">
                                </date-picker>
                              </div>

                            </div>
                            <div class="col-md-3">
                              <div class="form-group" :class="errors.has('ora_alle') ? 'has-error' : ''">
                                <label class="d-block w-100" :class="operation.status == 1 ? 'required': ''">Ora alle</label>

                                <date-picker
                                  tabindex="2"
	                                placeholder="alle"
	                                :disabled="operation.ora_dalle == ''"
	                                data-vv-as="(Alle)"
	                                key="data-input"
	                                v-model="operation.ora_alle"
	                                v-validate="{ required: (operation.status == 1 ? true : false), operationEndHour:true }"
	                                name="ora_alle"
	                                :config="btTimeOptions">
                                </date-picker>
                              </div>

                            </div>
                          </div>
						</div>

						<div class="col-md-12">
							<div class="row">
								<div class="col-md-4" :class="errors.has('client') ? 'has-error' : ''">
									<div class="form-group">
		                                <label class="required">
											Cliente
											<a :href="`/customer_add?backRoute=${url}`">Aggiungi</a>
											<a :href="`/customer_add/${operation.client.id}?backRoute=${url}`" v-if="operation.client">Modifica</a>
										</label>
		                                <v-select label="name"
												:filterable="false"
												:options="clients"
												@search="getClients"
												@change="getHeadquarters"
												v-model="operation.client"
												v-validate="'required'"
												data-vv-as="(Cliente)"
												name="client"
												placeholder="Inserisci un cliente">
		                                    <template slot="no-options">
		                                        nessun risultato trovato, inserire 3 caratteri...
		                                    </template>
		                                </v-select>
	                            	</div>
	                            </div>
								<div class="col-md-8" :class="errors.has('headquarter') ? 'has-error' : ''">
									<div class="form-group">
										<label class="required">Sede</label>
										<select class="form-control w-100" name="headquarter"
												v-model="operation.headquarter"
												v-validate="'required'"
												data-vv-as="(Sede)"
		                               			@change="getMachineries">
		                                	<option value=''></option>
											<option :value="headquarter.id" v-for="(headquarter, index) in headquarters" :key="index">
												{{ headquarter.fullData }}
											</option>
		                                </select>
	                            	</div>
								</div>
							</div>
						</div>
						<div class="col-md-12">
							<div class="col-md-12 bordered">
								<div class="col-md-12 mg-bot-20 mg-top-20">
									<div class="row">
										<div class="col-md-4">
											<label>Macchinari</label>
										</div>
										<div class="col-md-6">
											<label>Descrizione dell'intervento</label>
										</div>
										<div class="col-md-2">
											<button v-if="!operation.machineries.length" class="btn pull-right" @click="addMachinery">
												<i class="fas fa-plus"></i>
											</button>
										</div>
									</div>
								</div>
								<div class="col-md-12 mg-bot-20" style="align-items: flex-start !important"
										v-for="(machinary, index) in operation.machineries" :key="index">
									<div class="row form-group" :class="errors.has('machinery_id'+index) ? 'has-error' : ''">
										<div class="col-md-4">
											<select class="form-control w-100"
													:name="'machinery_id'+index"
													v-model="operation.machineries[index].id"
													v-validate="'required'">
												<option :value="machinary.id" v-for="(machinary, index) in machineries" :key="index">
													{{machinary.description}}
												</option>
											</select>
										</div>
										<div class="col-md-6">
											<textarea class="form-control" ref="description"
													v-model="machinary.description"
                          style="resize:vertical"
											></textarea>
										</div>
										<div class="col-md-2">
											<button class="btn btn-warning pull-right" @click="deleteMachinery(index)">
												<i class="fas fa-trash"></i>
											</button>
										</div>
									</div>
								</div>

								<div class="col-md-12 mg-bot-20" style="align-items: flex-start !important"
										v-if="!operation.machineries.length">
										<p style="text-align: center">Nessun macchinario scelto</p>
								</div>
							</div>
						</div>

                        <div class="col-md-12">
							<div class="row">
								<div class="col-md-12 d-flex">
									<div class="form-group" :class="errors.has('technician_1') ? 'has-error' : ''">
										<label for="technician_1" :class="operation.status == 1 ? 'required': ''">Tecnico responsabile</label>
										<select class="form-control w-100" name="technician_1"
												v-model="operation.technician_1"
												data-vv-as="(Tecnico)"
												v-validate="operation.status == 1? 'required': ''">
											<option value=''></option>
											<option :value="technician.id_user" v-for="(technician, index) in technicians" :key="index">
												{{technician.family_name+' '+technician.name}}
											</option>
										</select>
									</div>
									<div class="form-group">
										<label for="technician_2">Tecnico 2</label>
										<select class="form-control w-100"
												v-model="operation.technician_2">
											<option value=''></option>
											<option :value="technician.id_user" v-for="(technician, index) in technicians" :key="index">
												{{technician.family_name+' '+technician.name}}
											</option>
										</select>
									</div>
									<div class="form-group">
										<label for="technician_3">Tecnico 3</label>
										<select class="form-control w-100"
												v-model="operation.technician_3">
											<option value=''></option>
											<option :value="technician.id_user" v-for="(technician, index) in technicians" :key="index">
												{{technician.family_name+' '+technician.name}}
											</option>
										</select>
									</div>
                            	</div>
                          	</div>
                        </div>

                        <div class="col-md-12">
							<div class="row">
								<div class="col-md-12 d-flex">
                  <div class="form-group">
										<label for="stato">Da programmare</label>
										<select class="form-control w-100"
												v-model="operation.status">
                      <option value="0">Si</option>
											<option value="1" selected="selected">No</option>
										</select>
									</div>

									<div class="form-group">
										<label>Urgente</label>
										<select class="form-control w-100" v-model="operation.urgent">
											<option value="1">Si</option>
											<option value="0">No</option>
										</select>
									</div>
									<div class="form-group">
										<label>Fatturare a</label>
										<select class="form-control w-100" v-model="operation.invoiceTo">
											<option value='0' :selected="operation.invoiceTo == 0 ? 'true' : ''">Cliente</option>

											<option v-for="item in invoices_to" :value="item.id" :selected="operation.invoiceTo == item.id ? 'true' : ''">{{ item.ragione_sociale }}</option>
										</select>
									</div>
								</div>
							</div>
                        </div>

                        <div class="col-md-12">
							<div class="row">
								<div class="col-md-2">
									<div class="form-group">
										<label for="cestello">Cestello</label>
										<select name="cestello" class="form-control w-100" v-model="operation.cestello">
											<option value='0' selected="selected">No</option>
											<option value='1'>Si</option>
										</select>
									</div>
								</div>
                				<div class="col-md-2" v-if="!hideIncassoField">
									<div class="form-group">
										<label for="incasso">Incasso</label>
										<input type="number" name="incasso" id="incasso" class="form-control" step="0.01"
												v-model="operation.incasso"
												key="incasso-input"
										/>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group" :class="errors.has('note') ? 'has-error' : ''">
										<label for="note">Note</label>
										<textarea name="note" id="note" class="form-control" rows="5"
												v-model="operation.note"
												v-validate="{regex: /[0-9a-zA-Z\_\-\/\(\)\ \.\r\n]$/}"
												key="note-input"
										></textarea>
									</div>
								</div>
								<div class="col-md-4">
                                    <label class="">File:</label>
                                    <div class="input-group">
                                        <input class="form-control" type="text" name="fileName" v-model="operation.fileName" readonly />
                                        <input class="hidden" type="file" id="file" name="file" @change="onUploadFile" v-if="!operation.file" ref="operationFile">

                                        <span class="input-group-addon" style="padding:0px;">
											<a class="btn btn-sm btn-success"
													v-if="operation.file" target="_blank" download
													title="Scarica" :href="operation.url">
												<i class="fas fa-download"></i>
											</a>
											<label type="button" title="Carica" class="btn btn-sm btn-primary"
													v-if="!operation.file" for="file">
												<i class="fas fa-upload"></i>
											</label>
											<button type="button" class="btn btn-sm btn-warning" title="Elimina"
													v-if="operation.file" @click="onDeleteFile">
												<i class="fas fa-trash"></i>
											</button>
										</span>
                                    </div>
								</div>
							</div>
                        </div>
                        <div class="col-md-12">
							<button type="submit" class="btn btn-primary btn_general save-btn" @click="saveOperation" :disabled="save">
								<span v-if="!save"><i class="fa fa-save"></i>&nbsp;&nbsp;Salva</span>
								<i class="fas fa-spinner fa-pulse" v-if="save"></i>
							</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <i class="fa fa-calendar-alt"></i>
                    <h3 class="box-title"><strong> Lista degli interventi del {{operation.date | moment("dddd, DD MMMM YYYY") }}</strong></h3>
                </div>
                <div class="box-body">
                	<div class="table-responsive">
                    <table class="table table-responsive" v-if="operations.length">
                        <thead>
                            <tr>
                                <th>Ora</th>
                                <th>Tecnico</th>
                                <th>Cliente</th>
                                <th>Località</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(operation, index) in operations" :key="index">
                                <td>{{operation.ora_dalle + '-' + operation.ora_alle}}</td>
                                <td>
                                  <div  v-for="(technician, index) in operation.technicians" :key="index">
                                    {{technician.fullName}}
                                  </div>
                                </td>
                                <td>{{operation.clientName}}</td>
                                <td>{{operation.location}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                </div>
                <div class="overlay" v-if="loading" v-loading="loading">

                </div>
          </div>
        </div>
<!--         <sweet-modal ref="clienti" width="90%">
			<customer clienti-id="non"
					clienti-see="non"
					v-on:savedClient="atribClient($event)"
					v-on:closeModal="CloseClienti($event)"
					:nazionie="nazionie"
					:tip="'modal'"
			></customer>
        </sweet-modal> -->
    </div>
</template>

<script>
	import it from "vee-validate/dist/locale/it";
	import VeeValidate, { Validator } from "vee-validate";
	Vue.use(VeeValidate);

	Validator.localize("it", it);

	import datePicker from "vue-bootstrap-datetimepicker";
	Vue.use(datePicker); // Register datePicker
	import "@fortawesome/fontawesome-free/css/all.css";
	import "pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css";


	Vue.filter("uppercase", function(value) {
		return value.toUpperCase();
	});

	import "element-ui/lib/theme-chalk/index.css";

	import { SweetModal, SweetModalTab } from "sweet-modal-vue";

	export default {
		props: ["nazionie", "invoices_to","client_id"],
		components: {
			SweetModal,
			SweetModalTab
		},
		data() {
			return {
				url: window.location.pathname,
				btTimeOptions: {
					stepping: 15,
			        format: "HH:mm",
			        useCurrent: false,
			        icons: {
			          time: "far fa-clock",
			          date: "far fa-calendar",
			          up: "fas fa-arrow-up",
			          down: "fas fa-arrow-down",
			          previous: "fas fa-chevron-left",
			          next: "fas fa-chevron-right",
			          today: "fas fa-calendar-check",
			          clear: "far fa-trash-alt",
			          close: "far fa-times-circle",
			        },
					sideBySide: true,
			        //enabledHours: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22],
			        disabledTimeIntervals: [
				      [this.$moment({h:0}), this.$moment({h:2})],
				      [this.$moment({h:22}), this.$moment({h:24})]
				    ],
/*				    disabledTimeIntervals: [
				      [this.$moment("00:00"),this.$moment("01:00")],
				    ]*/
			    },
				pickerOptions: {
					disabledDate(time) {
						return time.getTime() <= Date.now() - 3600 * 1000 * 24;
					},
					firstDayOfWeek: 1,

				},

				clients: [],
				headquarters: [],
				machineries: [],
				technicians: [],
				show: false,
				message: false,
				info: "Nessuno",
				operations: [],
				loading: false,
				save: false,
				success: false,
				startHourValidation:{
					//required:true
				},
				endHourValidation:{
/*					required: () => {
						return operation.status == 1 ? true : false;
					}*/
				},
				operation: {
					tipologia:'',
					client: '',
					headquarter: '',
					machineries: [],
					urgent: 0,
					status: 1,
					date: '',
					ora_dalle: '',
					ora_alle: '',
					cestello: 0,
					incasso: 0.00,
					technician_1: '',
					technician_2: '',
					technician_3: '',
					bodyStatus: 0,
					invoiceTo: 0,
					note: "",
					file: '',
					fileName: '',
					url: ''
				},
        windowRef: ''
			};
		},
		components: {
		    datePicker,
		},
		watch: {
			operation: {
				deep: true,
				handler() {
					this.$cookies.set('operation', this.operation, "2h");
				}
			}
		},
    computed:{
      hideIncassoField(){
        return ['Sopralluogo Caldo','Sopralluogo Freddo'].includes(this.operation.tipologia)
      }
    },
		created(){

			Validator.extend('operationStartHour', {
			  getMessage: field => 'The ' + field + ' value is not truthy.',

			  validate: (value) => {

			    let time1 = parseInt(value.replace(':', ''));
			    let time2 = parseInt(this.operation.ora_alle.replace(':', ''));

			    var bool = time1 < time2 && (time1 >= 200 && time1 <= 2200); // check if value is between 02:00 and 22:00

			  	return bool;

			  }
			});

			Validator.extend('operationEndHour', {
			  getMessage: field => 'The ' + field + ' value is not truthy.',

			  validate: (value) => {

			    let time1 = parseInt(this.operation.ora_dalle.replace(':', ''));
			    let time2 = parseInt(value.replace(':', ''));

			    var bool = time2 > time1 && (time2 >= 200 && time2 <= 2200); // check if value is between 02:00 and 22:00

			  	return bool;

			  }
			});
		},
		methods: {
/*			timeAutoComplete(element, dropDownElement){

				element = document.querySelector('[name="'+element+'"]');

				var dropDownElements = document.querySelector('.' + dropDownElement).querySelector('.el-scrollbar .el-scrollbar__view');

				dropDownElements = dropDownElements.children;

				var scrollBar = document.querySelector('.' + dropDownElement).querySelector('.el-scrollbar .el-scrollbar__view');

				console.log(scrollBar);
				console.log(element.value);

				if(element.value){
					for(var i=0; i < dropDownElements.length; i++){

						console.log(dropDownElements[i].innerText);


						if(dropDownElements[i].innerText.startsWith(element.value)){


							var topPos = dropDownElements[i].scrollHeight;

							console.log('topPos= '+topPos);
							console.log(scrollBar.scrollTop);
							scrollBar.scrollTop += topPos;
							dropDownElements[i].scrollIntoView();

							console.log('time match!');

							continue;

						}
					}
				}

				//dropDownElement.scrollTop = 20;


			},*/
			focusDescriptionIndex(index){
				//this.$refs.description[0].focus();
				// console.log(this.$refs.description);

				this.$nextTick(() => {
			        this.$refs.description[index].focus();
			    })

			},
			getTechnicians() {
				axios.get("/interventi-get-tecnici").then(function(res) {
					this.technicians = res.data;
				}.bind(this));
			},
			getClients(search, loading) {
				loading(true);
				if (search.length > 1) {
					axios.post("/client-search", {
						value: search
					}).then(
						function(res) {
							this.clients = res.data;
							loading(false);
						}.bind(this)
					);
				}
			},
			getClientId(id) {
					axios.post("/client-search-id", {
						value: id
					}).then(
						function(res) {
							this.operation.client = res.data;
						}.bind(this)
					);
			},
			getHeadquarters() {
				this.operation.machineries = [];
				if(this.operation.client && this.operation.client.id) {
					axios.get(`/client/${this.operation.client.id}/locations`).then(function(res) {
						this.headquarters = res.data.locations;

						if(res.data.first_location && res.data.first_location != null){

							this.operation.headquarter = res.data.first_location;
							this.getMachineries();
						}

					}.bind(this));
				} else {
					this.operation.client = '';
					this.clients = [];
				}
			},
			getMachineries() {
				if(this.operation.headquarter) {
					axios.get(`/location/${this.operation.headquarter}/machineries`).then(function(res) {
						this.machineries = res.data.machineries.filter(item => item.tipologia == this.operation.tipologia);

						if(res.data.first_machinery && res.data.first_machinery != null){
							this.operation.machineries = [];
							this.operation.machineries.push({
								id: res.data.first_machinery.id_macchinario,
                tipologia: res.data.first_machinery.tipologia,
								description: ''
							});
							this.focusDescriptionIndex(0);
							//console.log(this.$refs["description"]);

						}


					}.bind(this));
				}
			},
			getOperationsByDate() {
				this.loading = true;
				axios.get(`/interventi-by-date?date=${this.operation.date}`).then(function(res) {
					if(res.data.length > 0) {
						setTimeout(function() {
							this.operations = res.data;
							this.loading = false;
						}.bind(this), 1000);
					} else {
						setTimeout(function() {
							this.loading = false;
							this.operations = [];
						}.bind(this), 1000);
					}
				}.bind(this));
			},
			openClientModal() {
				this.$refs.clienti.open();
			},
			atribClient(opt) {
				this.options = opt;
				this.selected = this.options[0];
				this.$refs.clienti.close();
				this.$notify({
					title: "",
					message: "Il cliente è stato aggiunto correttamente",
					type: "success"
				});
			},
			CloseClienti(ev) {
				if (ev == "close") {
					this.$refs.clienti.close();
				}
			},
			saveOperation() {
				this.$validator.validate().then(result => {
					if (result) {
						if(this.$cookies.isKey('operation')) {
							this.$cookies.remove('operation');
						}
						let formData = new FormData();
						formData.append('file', this.operation.file);
						formData.append('operation', JSON.stringify(this.operation));
						this.save = !this.save;
						axios.post("/interventi-save", formData, {
								headers: {
									'Content-Type': 'multipart/form-data'
								}
							}).then(function(res) {
							this.$notify({
								title: "Intervento",
								message: "è stato registrato con successo",
								type: "success"
							});

							if(document.referrer !== ""){
                var backPage = new URL(document.referrer);
                if(backPage.pathname == "/monitoring")
                  window.location.href = backPage.pathname;  //document.referrer
                else
                  location.reload();
              }
              else
                location.reload();





						}.bind(this)).catch(function(res) {
							this.$notify.error({
								title: "Errore",
								message: "Intervento non è stato registrato"
							});
							this.save = !this.save;
						}.bind(this));
					}
				});
			},
			addMachinery() {
				this.operation.machineries.push({
					id: 0,
					description: ''
				});
			},
			deleteMachinery(index) {
				this.operation.machineries.splice(index, 1);
			},
			onUploadFile() {
				this.operation.file = this.$refs.operationFile.files[0];
				this.operation.fileName = this.$refs.operationFile.files[0].name;
				this.operation.url = URL.createObjectURL(this.operation.file);
			},
			onDeleteFile() {
				this.operation.file = '';
				this.operation.fileName = '';
				this.operation.url = '';
			}
		},
		mounted() {
			$('[data-toggle="tooltip"]').tooltip();
      var backPage = null;

      if(this.client_id){
        this.$cookies.remove('operation');
        this.getClientId(this.client_id);
        this.operation.date = this.$moment().format("YYYY-MM-DD");
      }

      if(document.referrer !== ""){
        backPage =  new URL(document.referrer);

       if(backPage.pathname == "/monitoring"){
         this.$cookies.remove('operation');
         this.operation.date = (this.$cookies.isKey('monitoringDate')) ? this.$cookies.get('monitoringDate') : this.$moment().format("YYYY-MM-DD");
       }
      }



			if(this.$cookies.isKey('operation')) {
				this.operation.tipologia = this.$cookies.get('operation').tipologia;
				this.operation.ora_dalle = this.$cookies.get('operation').ora_dalle;
				this.operation.ora_alle = this.$cookies.get('operation').ora_alle;
				this.operation.incasso = this.$cookies.get('operation').incasso;
				this.operation.cestello = this.$cookies.get('operation').cestello;
				this.operation.client = this.$cookies.get('operation').client;
				this.operation.headquarter = this.$cookies.get('operation').headquarter;
				this.operation.machineries = this.$cookies.get('operation').machineries;
				this.operation.urgent = this.$cookies.get('operation').urgent;
				this.operation.status = this.$cookies.get('operation').status;
				this.operation.date = this.$cookies.get('operation').date;
				this.operation.technician_1 = this.$cookies.get('operation').technician_1;
				this.operation.technician_2 = this.$cookies.get('operation').technician_2;
				this.operation.technician_3 = this.$cookies.get('operation').technician_3;
				this.operation.bodyStatus = this.$cookies.get('operation').bodyStatus;
				this.operation.invoiceTo = this.$cookies.get('operation').invoiceTo;
				this.operation.note = this.$cookies.get('operation').note;
				this.operation.file = this.$cookies.get('operation').file;
				this.operation.fileName = this.$cookies.get('operation').fileName;
				this.operation.url = this.$cookies.get('operation').url;
				if(this.operation.headquarter) {
					this.getMachineries();
				}
				this.$cookies.remove('operation');
			}

			this.getOperationsByDate();
			this.getTechnicians();



/*			document.querySelector('[name="ora_dalle"]').addEventListener('keyup', (e) => {
				var element = e.target.getAttribute('name');
				console.log(element);
				this.timeAutoComplete(element, 'timeSelect1');
			});

			document.querySelector('[name="ora_alle"]').addEventListener('keyup', (e) => {
				var element = e.target.getAttribute('name');
				this.timeAutoComplete(element, 'timeSelect2');
			});*/

		}
	};


</script>

<style lang="scss">
	.sweet-modal {
		max-width: 80% !important;
	}
	.yearMonthModal {
		background: rgba(0, 0, 0, 0.3) !important;
		.sweet-modal {
			box-shadow: 0 2px 3px rgba(0, 0, 0, 0.125) !important;
			max-width: 80% !important;
		}
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
				background-color: #ff851b;
				h3 {
					margin: 0 !important;
					line-height: 1.42857143 !important;
					color: #ffffff !important;
				}
			}

			.content {
				min-height: 110px;
				display: flex;
				align-items: center;
				justify-content: center;
			}
		}
	}
	.mg-bot-20 {
		margin-bottom: 20px;
	}
	.mg-top-20 {
		margin-top: 20px;
	}
	.bordered {
		outline: 2px solid #bbbbbb;
		box-sizing: border-box;
	}
	.plus {
		width: 40px !important;
		margin-top: 24px;
		margin-left: 0px!important;
	}

	.break {
	  flex-basis: 100%;
	  height: 0;
	}
</style>
