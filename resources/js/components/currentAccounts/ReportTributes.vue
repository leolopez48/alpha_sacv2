<template>
  <v-row>
    <v-col
      cols="12"
      sm="12"
      md="6"
      lg="6"
      xl="6"
      offset-md="3"
      offset-lg="3"
      offset-xl="3"
    >
      <v-card class="p-3">
        <h2 class="p-3">Reporte de ingresos por tributos</h2>
        <v-col cols="12" sm="12" md="12" align="center">
          <base-input
            label="Fecha inicio"
            v-model="$v.editedItem.dateStart.$model"
            :validation="$v.editedItem.dateStart"
            validationTextType="default"
            type="date"
            :validationsInput="{
              required: true,
              minLength: true,
            }"
          />
        </v-col>

        <v-col cols="12" sm="12" md="12">
          <base-input
            label="Fecha final"
            v-model="$v.editedItem.dateEnd.$model"
            :validation="$v.editedItem.dateEnd"
            validationTextType="default"
            type="date"
            :validationsInput="{
              required: true,
              minLength: true,
            }"
          />
        </v-col>

        <v-btn rounded class="btn btn-normal" @click="filterByDates"
          >Generar reporte</v-btn
        >
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
import { required, minLength, maxLength } from "vuelidate/lib/validators";
import moment from "moment";

export default {
  data() {
    return {
      // template to filter by dates
      editedItem: {
        dateStart: moment().format("YYYY-MM-DD"),
        dateEnd: moment().format("YYYY-MM-DD"),
      },
    };
  },

  validations: {
    editedItem: {
      dateStart: {
        required,
        minLength: minLength(1),
      },
      dateEnd: {
        required,
        minLength: minLength(1),
      },
    },
  },

  methods: {
    // template to filter by dates
    filterByDates() {
      this.$v.$touch();

      if (this.$v.$invalid) {
        return;
      }
      window.open(
        `/reportByDate?dateStart=${this.editedItem.dateStart}&dateEnd=${this.editedItem.dateEnd}`
      );
    },
  },
};
</script>

<style>
</style>
