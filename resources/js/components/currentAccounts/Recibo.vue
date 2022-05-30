<template>
  <div data-app>
    <alert-time-out
      :redirect="redirectSessionFinished"
      @redirect="updateTimeOut($event)"
    />
    <alert
      :text="textAlert"
      :event="alertEvent"
      :show="showAlert"
      @show-alert="updateAlert($event)"
      class="mb-2"
    />
    <v-data-table
      :headers="headers"
      :items="recordsFiltered"
      sort-by="id"
      class="elevation-3 shadow p-3 mt-3"
    >
      <template v-slot:top>
        <v-toolbar flat>
          <v-toolbar-title>Recibos</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-dialog v-model="dialog" max-width="600px" persistent>
            <template v-slot:activator="{ on, attrs }">
              <v-row>
                <v-col align="end">
                  <v-btn
                    class="mb-2 btn-normal no-uppercase"
                    v-bind="attrs"
                    v-on="on"
                    rounded
                    @click="$v.editedItem.$reset()"
                  >
                    Agregar
                  </v-btn>
                </v-col>
                <v-col
                  xs="6"
                  sm="6"
                  md="6"
                  class="d-none d-md-block d-lg-block"
                >
                  <v-text-field
                    dense
                    label="Buscar"
                    outlined
                    type="text"
                    class=""
                    v-model="search"
                    @keyup="searchValue()"
                  ></v-text-field>
                </v-col>
              </v-row>
            </template>
            <v-card class="flexcard" height="100%">
              <v-card-title>
                <h1 class="mx-auto pt-3 mb-3 text-center black-secondary">
                  {{ formTitle }}
                </h1>
              </v-card-title>

              <v-card-text>
                <v-container>
                  <!-- Form -->
                  <v-row>
                    <!-- Fecha registro -->
                    <v-col cols="12" sm="12" md="6">
                      <base-input
                        label="Fecha registro"
                        v-model="$v.editedItem.fecha_registro.$model"
                        :validation="$v.editedItem.fecha_registro"
                        validationTextType="default"
                        type="date"
                        :validationsInput="{
                          required: true,
                          minLength: true,
                          maxLength: true,
                        }"
                      />
                    </v-col>
                    <!-- Fecha registro -->
                    <!-- DUI -->
                    <v-col cols="12" sm="12" md="6">
                      <base-input
                        label="DUI"
                        :validation="$v.editedItem.dui"
                        validationTextType="only-numbers"
                        v-mask="'########-#'"
                        v-model="$v.editedItem.dui.$model"
                        :validationsInput="{
                          required: true,
                          minLength: true,
                          maxLength: true,
                        }"
                      />
                    </v-col>
                    <!-- DUI -->
                    <!-- Nombres -->
                    <v-col cols="12" sm="12" md="6">
                      <base-input
                        label="Nombre"
                        :validation="$v.editedItem.nombres"
                        validationTextType="default"
                        v-model="$v.editedItem.nombres.$model"
                        :validationsInput="{
                          required: true,
                          minLength: true,
                          maxLength: true,
                        }"
                      />
                    </v-col>
                    <!-- Nombres -->
                    <!-- Apellidos -->
                    <v-col cols="12" sm="12" md="6">
                      <base-input
                        label="Apellidos"
                        :validation="$v.editedItem.apellidos"
                        validationTextType="default"
                        v-model="$v.editedItem.apellidos.$model"
                        :validationsInput="{
                          required: true,
                          minLength: true,
                          maxLength: true,
                        }"
                      />
                    </v-col>
                    <!-- Apellidos -->
                    <!-- Direccion -->
                    <v-col cols="12" sm="12" md="12">
                      <base-input
                        label="Dirección"
                        v-model="$v.editedItem.direccion.$model"
                        :validation="$v.editedItem.direccion"
                        validationTextType="default"
                        :validationsInput="{
                          required: true,
                          minLength: true,
                          maxLength: true,
                        }"
                      />
                    </v-col>
                    <!-- Direccion -->
                    <!-- Concepto -->
                    <v-col cols="12" sm="12" md="12">
                      <base-input
                        label="Concepto"
                        v-model="$v.editedItem.concepto.$model"
                        :validation="$v.editedItem.concepto"
                        validationTextType="default"
                        :validationsInput="{
                          required: true,
                          minLength: true,
                          maxLength: true,
                        }"
                      />
                    </v-col>
                    <!-- Concepto -->

                    <hr />
                    <h2>Detalles</h2>
                    <add-accounts />
                  </v-row>
                  <!-- Form -->
                  <v-row>
                    <v-col align="center">
                      <v-btn
                        color="btn-normal no-uppercase mt-3"
                        rounded
                        @click="save"
                      >
                        Guardar
                      </v-btn>

                      <v-btn
                        color="btn-normal-close no-uppercase mt-3"
                        rounded
                        @click="close"
                      >
                        Cancelar
                      </v-btn>
                    </v-col>
                  </v-row>
                </v-container>
              </v-card-text>
            </v-card>
          </v-dialog>
          <v-dialog v-model="dialogDelete" max-width="400px">
            <v-card class="h-100">
              <v-container>
                <h3 class="black-secondary text-center mt-3 mb-3">
                  Eliminar registro
                </h3>
                <v-row>
                  <v-col align="center">
                    <v-btn
                      color="btn-normal no-uppercase mt-3 mb-3 pr-5 pl-5 mx-auto"
                      rounded
                      @click="deleteItemConfirm"
                      >Confirmar</v-btn
                    >
                    <v-btn
                      color="btn-normal-close no-uppercase mt-3 mb-3"
                      rounded
                      @click="closeDelete"
                    >
                      Cancelar
                    </v-btn>
                  </v-col>
                </v-row>
              </v-container>
            </v-card>
          </v-dialog>
        </v-toolbar>
      </template>
      <template v-slot:[`item.actions`]="{ item }">
        <v-icon small class="mr-2" @click="editItem(item)"> mdi-pencil </v-icon>
        <v-icon small @click="deleteItem(item)"> mdi-delete </v-icon>
      </template>
      <template v-slot:no-data>
        <a
          href="#"
          class="btn btn-normal-secondary no-decoration"
          @click="initialize"
        >
          Reiniciar
        </a>
      </template>
    </v-data-table>
  </div>
</template>

<script>
import reciboApi from "../../apis/reciboApi";
import cuentaApi from "../../apis/cuentaApi";
import lib from "../../libs/function";
import { required, minLength, maxLength } from "vuelidate/lib/validators";
import AddAccounts from "./AddAccounts.vue";

export default {
  components: { AddAccounts },
  data: () => ({
    search: "",
    dialog: false,
    dialogDelete: false,
    headers: [
      { text: "FECHA", value: "fecha_registro" },
      { text: "DUI", value: "dui" },
      { text: "NOMBRES", value: "nombres" },
      { text: "APELLIDOS", value: "apellidos" },
      { text: "DIRECCION", value: "direccion" },
      { text: "ACCIONES", value: "actions", sortable: false },
    ],
    records: [],
    recordsFiltered: [],
    editedIndex: -1,
    editedItem: {
      fecha_registro: "",
      dui: "",
      nombres: "",
      apellidos: "",
      direccion: "EL PARAISO",
      concepto: "",
      total: "",
      details_receipts: [],
    },
    defaultItem: {
      fecha_registro: "",
      dui: "",
      nombres: "",
      apellidos: "",
      direccion: "EL PARAISO",
      concepto: "",
      total: "",
      details_receipts: [],
    },
    textAlert: "",
    alertEvent: "success",
    showAlert: false,
    redirectSessionFinished: false,
    alertTimeOut: 0,
    accounts: [],
  }),

  //Validations
  validations: {
    editedItem: {
      fecha_registro: {
        required,
        minLength: minLength(1),
      },
      dui: {
        required,
        minLength: minLength(1),
        maxLength: maxLength(150),
      },
      nombres: {
        required,
        minLength: minLength(1),
        maxLength: maxLength(150),
      },
      apellidos: {
        required,
        minLength: minLength(1),
        maxLength: maxLength(150),
      },
      direccion: {
        required,
        minLength: minLength(1),
        maxLength: maxLength(150),
      },
      concepto: {
        required,
        minLength: minLength(1),
        maxLength: maxLength(150),
      },
      total: {
        required,
        minLength: minLength(1),
      },
    },
  },
  //Validations
  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "Nuevo registro" : "Editar registro";
    },
  },

  watch: {
    dialog(val) {
      val || this.close();
    },
    dialogDelete(val) {
      val || this.closeDelete();
    },
  },

  created() {
    this.initialize();
  },

  methods: {
    async initialize() {
      this.records = [];
      this.recordsFiltered = [];

      let requests = [reciboApi.get(), cuentaApi.get()];

      const res = await Promise.all(requests).catch((error) => {
        this.updateAlert(true, "No fue posible obtener los registros.", "fail");
        this.redirectSessionFinished = lib.verifySessionFinished(
          error.response.status,
          401
        );
      });

      this.records = res[0].data.recibos;
      this.recordsFiltered = res[0].data.recibos;
      this.accounts = res[1].data.cuentas;
    },

    editItem(item) {
      this.editedIndex = this.recordsFiltered.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
      this.$v.editedItem.$reset();
    },

    deleteItem(item) {
      this.editedIndex = this.recordsFiltered.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialogDelete = true;
    },

    async deleteItemConfirm() {
      const res = await reciboApi
        .delete(`/${this.editedItem.id}`)
        .catch((error) => {
          this.updateAlert(
            true,
            "No fue posible eliminar el registros.",
            "fail"
          );
          this.close();
          this.redirectSessionFinished = lib.verifySessionFinished(
            error.response.status,
            419
          );
        });

      if (res.data.status == "success") {
        this.redirectSessionFinished = lib.verifySessionFinished(
          res.status,
          200
        );
        this.updateAlert(true, "Registro eliminado.", "success");
      }

      this.initialize();
      this.closeDelete();
    },

    close() {
      this.dialog = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },

    closeDelete() {
      this.dialogDelete = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },

    async save() {
      this.$v.$touch();
      if (this.$v.$invalid) {
        this.updateAlert(true, "Campos obligatorios.", "fail");
        return;
      }

      if (this.editedIndex > -1) {
        const edited = Object.assign(
          this.recordsFiltered[this.editedIndex],
          this.editedItem
        );

        const res = await reciboApi
          .put(`/${this.editedItem.id}`, this.editedItem)
          .catch((error) => {
            this.updateAlert(
              true,
              "No fue posible actualizar el registro.",
              "fail"
            );

            this.redirectSessionFinished = lib.verifySessionFinished(
              error.response.status,
              419
            );
          });

        if (res.data.status == "success") {
          this.redirectSessionFinished = lib.verifySessionFinished(
            res.status,
            200
          );
          this.updateAlert(true, "Registro actualizado.", "success");
        }
      } else {
        const res = await reciboApi
          .post(null, this.editedItem)
          .catch((error) => {
            this.updateAlert(true, "No fue posible crear el registro.", "fail");
            this.close();
            this.redirectSessionFinished = lib.verifySessionFinished(
              error.response.status,
              419
            );
          });

        if (res.data.status == "success") {
          this.redirectSessionFinished = lib.verifySessionFinished(
            res.status,
            200
          );
          this.updateAlert(
            true,
            "Registro almacenado correctamente.",
            "success"
          );
        }
      }

      this.close();
      this.initialize();
      return;
    },

    searchValue() {
      this.recordsFiltered = [];

      if (this.search != "") {
        this.records.forEach((record) => {
          let searchConcat = "";
          for (let i = 0; i < record.fecha_registro.length; i++) {
            searchConcat += record.fecha_registro[i].toUpperCase();
            if (
              searchConcat === this.search.toUpperCase() &&
              !this.recordsFiltered.some((rec) => rec == record)
            ) {
              this.recordsFiltered.push(record);
            }
          }
        });
        return;
      }

      this.recordsFiltered = this.records;
    },

    updateAlert(show = false, text = "Alerta", event = "success") {
      this.textAlert = text;
      this.alertEvent = event;
      this.showAlert = show;
    },

    updateTimeOut(event) {
      this.redirectSessionFinished = event;
    },
  },
};
</script>

