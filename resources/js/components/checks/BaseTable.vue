<template>
  <div class="table-responsive">
    <table class="table table-striped table-hover mt-3">
      <thead>
        <tr>
          <th v-for="(header, index) in headers" :key="index">{{ header }}</th>
        </tr>
      </thead>
      <tbody v-if="data">
        <tr v-if="data.length == 0">
          <td :colspan="headers.length" class="text-center">
            No se encontraron registros por mostrar.
          </td>
        </tr>
        <tr v-for="(value, index) in data" :key="value.id">
          <td>{{ index + 1 }}</td>
          <td>{{ value.name_supplier }}</td>
          <td>{{ value.dui }}</td>
          <td>{{ value.nit }}</td>
          <td>{{ value.address }}</td>
          <td>
            <a href="#" class="btn btn-warning" @click="edit(value.id)"
              ><i class="fa fa-edit"></i> Editar</a
            >
          </td>
          <td>
            <a href="#" class="btn btn-danger" @click="deleteValue(value.id)"
              ><i class="fa fa-trash"></i> Eliminar</a
            >
          </td>
          <!-- <td>
            <a :href="`/generatePDFStudent/${value.id}`" class="btn btn-info">
              <i class="fa fa-doc"></i> Reporte</a
            >
          </td> -->
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  props: {
    headers: {
      type: Array,
      required: false,
      default: () => [],
    },
    data: {
      type: Array,
      required: true,
      default: () => [],
    },
  },
  mounted() {
    console.log(this.data);
  },
  methods: {
    async deleteValue(id) {
      this.$emit("delete", id);
    },
    async edit(id) {
      this.$emit("edit", id);
    },
  },
};
</script>

<style>
</style>
