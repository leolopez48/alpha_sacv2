<template>
  <div>
    <v-row>
      <v-col cols="12" sm="12" md="8" lg="8" xl="8">
        <base-select-search
          label="Cuentas"
          v-model.trim="$v.newDetail.nombre_cuenta.$model"
          :items="accounts"
          item="nombre_cuenta"
          :validation="$v.newDetail.nombre_cuenta"
          :validationsInput="{
            required: true,
            minLength: true,
            maxLength: true,
          }"
        />
      </v-col>

      <v-col cols="12" sm="12" md="4" lg="4" xl="4">
        <base-input
          label="Cantidad"
          v-model="$v.newDetail.cantidad.$model"
          :validation="$v.newDetail.cantidad"
          validationTextType="only-numbers"
          :validationsInput="{
            required: true,
            minLength: true,
            maxLength: true,
          }"
        />
      </v-col>
    </v-row>

    <a href="#" class="btn btn-normal mb-3 mt-4" @click="addNewDetail()"
      >Agregar</a
    >

    <table class="table table-striped table-hover">
      <thead>
        <th>Nombre</th>
        <th>Valor</th>
        <th>Cantidad</th>
        <th>Total</th>
        <th>Acciones</th>
      </thead>
      <tbody v-if="editedItem.detail_receipts.length > 0">
        <tr v-for="(detail, index) in editedItem.detail_receipts" :key="index">
          <td>{{ detail.nombre_cuenta }}</td>
          <td>{{ detail.valor }}</td>
          <td>{{ detail.cantidad }}</td>
          <td>{{ detail.subtotal }}</td>
          <td>
            <a href="#" @click="deleteDetail(index)"
              ><i class="material-icons">delete</i></a
            >
          </td>
        </tr>
        <tr>
          <td colspan="3" class="text-right">Total</td>
          <td colspan="2">${{ amountFormat() }}</td>
        </tr>
      </tbody>
      <tbody v-else>
        <tr>
          <td colspan="5">No se encontraron detalles por mostrar.</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import cuentaApi from "../../apis/cuentaApi";
import { required, minLength, maxLength } from "vuelidate/lib/validators";

export default {
  data: () => ({
    accounts: [],
    newDetail: {
      nombre_cuenta: "",
      valor: 0.0,
      cantidad: 1,
      subtotal: 0.0,
    },
    newDetailDefault: {
      nombre_cuenta: "",
      valor: 0.0,
      cantidad: 1,
      subtotal: 0.0,
    },
    fiesta: 0.05,
    total: 0.0,
  }),

  props: {
    editedItem: {
      type: Object,
      default: () => ({
        detail_receipts: [],
      }),
    },
  },

  validations: {
    newDetail: {
      nombre_cuenta: {
        required,
        minLength: minLength(1),
        maxLength: maxLength(255),
      },
      cantidad: {
        required,
        minLength: minLength(1),
        maxLength: maxLength(255),
      },
    },
  },

  watch: {
    editedItem(val) {
      this.total = 0.0;
      val.detail_receipts.forEach((detail) => {
        this.total += detail.subtotal;
        // console.log(this.total);
      });
    },
  },

  created() {
    this.total = 0.0;
    this.editedItem.detail_receipts.forEach((detail) => {
      this.total += detail.subtotal;
    });
  },

  mounted() {
    this.initialize();
  },

  methods: {
    async initialize() {},
  },

  methods: {
    async initialize() {
      this.records = [];
      this.recordsFiltered = [];

      console.log("Hola, ", this.editedItem);

      let requests = [cuentaApi.get()];

      const res = await Promise.all(requests).catch((error) => {
        this.updateAlert(true, "No fue posible obtener los registros.", "fail");
        this.redirectSessionFinished = lib.verifySessionFinished(
          error.response.status,
          401
        );
      });

      this.accounts = res[0].data.cuentas;
    },

    addNewDetail() {
      this.$v.$touch();
      if (this.$v.$invalid) {
        return;
      }

      this.accounts.filter((account) => {
        if (account.nombre_cuenta == this.newDetail.nombre_cuenta) {
          this.newDetail.valor = account.valor;
          this.newDetail.subtotal = Number.parseFloat(
            account.valor * this.newDetail.cantidad
          );

          if (account.apply_parties == 1) {
            let fiesta = {
              nombre_cuenta: `FIESTA`,
              valor: this.fiesta,
              cantidad: this.newDetail.cantidad,
              subtotal: parseFloat(
                parseFloat(this.newDetail.subtotal).toFixed(3) * this.fiesta
              ).toFixed(2),
            };

            this.editedItem.detail_receipts.push(fiesta);
            this.total += parseFloat(fiesta.subtotal).toFixed(2);
          }

          this.total += parseFloat(this.newDetail.subtotal, 2).toFixed(2);
        }
      });

      this.editedItem.detail_receipts.push(this.newDetail);
      this.$v.$reset();
      this.newDetail = this.newDetailDefault;

      this.$emit("add-new-detail", {
        receipts: this.editedItem.detail_receipts,
        total: this.total,
      });
    },

    deleteDetail(index) {
      this.total -= this.editedItem.detail_receipts[index].subtotal;

      this.editedItem.detail_receipts.splice(index, 1);
    },

    amountFormat() {
      return Number.parseFloat(this.total).toFixed(2);
    },
  },
};
</script>

<style>
</style>
