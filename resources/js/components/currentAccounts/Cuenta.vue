<template>
  <div data-app>
    <v-data-table
      :headers="headers"
      :items="recordsFiltered"
      sort-by="nombre_cuenta"
      class="elevation-3 shadow p-3 mt-3"
    >
      <template v-slot:top>
        <v-toolbar flat>
          <v-toolbar-title>Cuentas</v-toolbar-title>
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
                    <!-- Name -->
                    <v-col cols="12" sm="12" md="12">
                      <base-input
                        label="Código"
                        v-model="$v.editedItem.codigo.$model"
                        :validation="$v.editedItem.codigo"
                        validationTextType="none"
                        :validationsInput="{
                          required: true,
                          minLength: true,
                          maxLength: true,
                        }"
                      />
                    </v-col>
                    <!-- Name -->
                    <!-- Name -->
                    <v-col cols="12" sm="12" md="12">
                      <base-input
                        label="Nombre"
                        v-model="$v.editedItem.nombre_cuenta.$model"
                        :validation="$v.editedItem.nombre_cuenta"
                        validationTextType="none"
                        :validationsInput="{
                          required: true,
                          minLength: true,
                          maxLength: true,
                        }"
                      />
                    </v-col>
                    <!-- Name -->
                    <!-- Value -->
                    <v-col cols="12" sm="12" md="12">
                      <base-input
                        label="Valor"
                        v-model="$v.editedItem.valor.$model"
                        :validation="$v.editedItem.valor"
                        validationTextType="numbers-dot"
                        type="number"
                        step="0.01"
                        :validationsInput="{
                          required: true,
                          minLength: true,
                          maxLength: true,
                        }"
                      />
                    </v-col>
                    <!-- Value -->
                    <!-- May Name -->
                    <v-col cols="12" sm="12" md="12">
                      <v-checkbox
                        v-model="$v.editedItem.apply_parties.$model"
                        label="Aplicar fiestas"
                      ></v-checkbox>
                    </v-col>
                    <!-- May Name -->
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
import cuentaApi from "../../apis/cuentaApi";
import ui from "../../libs/ui";
import { required, minLength, maxLength } from "vuelidate/lib/validators";

export default {
  data: () => ({
    search: "",
    dialog: false,
    dialogDelete: false,
    headers: [
      { text: "CODIGO", value: "codigo" },
      { text: "NOMBRE", value: "nombre_cuenta" },
      { text: "VALOR", value: "valor" },
      { text: "ACCIONES", value: "actions", sortable: false },
    ],
    records: [],
    recordsFiltered: [],
    editedIndex: -1,
    editedItem: {
      codigo: "",
      nombre_cuenta: "",
      valor: "",
      apply_parties: true,
    },
    defaultItem: {
      codigo: "",
      nombre_cuenta: "",
      valor: "",
      apply_parties: true,
    },
    textAlert: "",
    alertEvent: "success",
    showAlert: false,
    redirectSessionFinished: false,
    alertTimeOut: 0,
  }),

  //Validations
  validations: {
    editedItem: {
      codigo: {
        required,
        minLength: minLength(1),
        maxLength: maxLength(255),
      },
      nombre_cuenta: {
        required,
        minLength: minLength(1),
        maxLength: maxLength(150),
      },
      valor: {
        required,
        minLength: minLength(1),
        maxLength: maxLength(150),
      },
      apply_parties: {
        required,
        minLength: minLength(1),
        maxLength: maxLength(150),
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

      const res = await cuentaApi.get().catch((error) => {
        ui.alert("No fue posible obtener los registros.", "error");
      });

      this.records = res.data.cuentas;
      this.recordsFiltered = res.data.cuentas;
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
      const res = await cuentaApi
        .delete(`/${this.editedItem.id}`)
        .catch((error) => {
          ui.alert("Registro no pudo ser eliminado correctamente.", "error");
        });

      if (res.data.status == "success") {
        ui.alert("Registro eliminado correctamente.");
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

        const res = await cuentaApi
          .put(`/${this.editedItem.id}`, this.editedItem)
          .catch((error) => {
            ui.alert(
              "Registro no pudo ser actualizado correctamente.",
              "error"
            );
          });

        if (res.data.status == "success") {
          ui.alert("Registro actualizado correctamente.");
        }
      } else {
        const res = await cuentaApi
          .post(null, this.editedItem)
          .catch((error) => {
            ui.alert("Registro no pudo ser almacenado correctamente.", "error");
          });

        if (res.data.status == "success") {
          ui.alert("Registro almacenado correctamente.");
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
          for (let i = 0; i < record.nombre_cuenta.length; i++) {
            searchConcat += record.nombre_cuenta[i].toUpperCase();
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
  },
};
</script>

