import {FileIcon} from "@/Helpers/Partials/Icons/AppIcons.js";
const files = {
    upload(filesToUpload, folder = null, item = null) {
        var formData = new FormData();
        filesToUpload.map((x) => formData.append("files[]", x));
        formData.append("model", route().current().split(".")[0]);
        formData.append("id", !!item ? item.id : "");
        formData.append("folder", !!folder ? folder.id : "");
        formData.append("inFolder", !!folder ? "1" : "0");
        return axios.post(route("folder.upload"), formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
    },
    delete(file) {
        return axios.delete(route("folder.delete_file", file.id));
    },
    updateName(file, name) {
        return axios.put(route("folder.updateFileName"), {
            id: file.id,
            name: name,
        });
    },
    dragAnddrop: {
        dragover: (event) => {
            event.preventDefault();
            // Add some visual fluff to show the user can drop its files
            if (!event.currentTarget.classList.contains("bg-green-300")) {
                event.currentTarget.classList.remove("bg-white");
                event.currentTarget.classList.add("bg-green-300");
            }
        },
        dragleave: (event) => {
            // Clean up
            event.currentTarget.classList.add("bg-white");
            event.currentTarget.classList.remove("bg-green-300");
        },
        drop: (event) => {
            event.preventDefault();
            //this.onChange(); // Trigger the onChange event manually
            // Clean up
            //console.log('qie', event.dataTransfer.files);
            event.currentTarget.classList.add("bg-white");
            event.currentTarget.classList.remove("bg-green-300");
            return [...event.dataTransfer.files];
        },
    },
};

const folder = {
    all() {
        return axios.get(route("folder.main"));
    },
    files(f) {
        return axios.get(route("folder.files", f.id));
    },
    new(folder, name) {
        return axios.post(route("folder.store"), {
            name: name,
            parent_id: !!folder ? folder.id : null,
        });
    },
    updateName(folder, name) {
        folder.name = name;
        return axios.put(route("folder.update", folder.id), folder);
    },
    delete(folder) {
        return axios.delete(route("folder.destroy", folder.id));
    }
};

const Explore = {
    emits: ["change"],
    components: { FileIcon },
    template: `
         <label for="assetsFieldHandle" class="cursor-pointer flex">
            <FileIcon class="w-4 h-4 mr-2" />
            Explorar
            <input type="file" multiple name="fields[assetsFieldHandle][]" id="assetsFieldHandle" class="w-px h-px opacity-0 overflow-hidden absolute" @change="$emit('change', $event)" ref="file" />
        </label>
        `,
};

export { files, Explore, folder };
