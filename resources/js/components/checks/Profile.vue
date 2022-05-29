<template>
  <div class="row" v-if="usuarioActual">
    <div class="row">
      <div class="col-md-12"><b>Nombre:</b> {{ usuarioActual.name }}</div>
      <div class="col-md-12"><b>Correo:</b> {{ usuarioActual.email }}</div>
      <div class="col-md-4">
        <b>Contraseña:</b>
        <input
          :type="showPassword"
          class="form-control"
          v-model="usuarioActual.password"
        />
        <a href="#" @click="showHidePassword">Mostrar constraseña</a>
      </div>
    </div>
    <div class="col-md-12 text-center">
      <a href="#" class="btn btn-primary" @click="updateUsuarioActual"
        >Actualizar</a
      >
    </div>
  </div>
</template>

<script>
import ui from "../../libs/ui";

export default {
  data() {
    return {
      usuarioActual: {},
      showPassword: "password",
    };
  },
  mounted() {
    this.init();
  },
  methods: {
    async init() {
      let res = await axios.get("/usuarioActual");
      console.log(res.data.name);
      if (res) {
        this.usuarioActual = res.data;
      }
    },
    showHidePassword() {
      this.showPassword = this.showPassword == "password" ? "text" : "password";
    },
    async updateUsuarioActual() {
      if (
        this.usuarioActual.password == "" ||
        !this.usuarioActual.password ||
        this.usuarioActual.password.length < 8
      ) {
        ui.alert(
          "Debes asignar una contraseña y debe contener al menos 8 carácteres.",
          "error"
        );
        return;
      }
      const res = await axios
        .put(`/api/user/${this.usuarioActual.id}`, this.usuarioActual)
        .catch((error) => {
          ui.alert("No fue posible actualizar los datos.", "error");
        });
      if (res && res.data.mensaje == "exito") {
        ui.alert("Registro modificado exitosamente.", "success");
      }
      setTimeout(() => {
        window.location = "/";
      }, 3000);
    },
  },
};
</script>

<style>
</style>
