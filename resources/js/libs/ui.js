import Swal from "sweetalert2";

export default {
    alert: (title = "", icon = "success") => {
        Swal.fire({
            position: "top-end",
            icon: icon,
            title: title,
            showConfirmButton: false,
            timer: 1500,
        });
    },
};
