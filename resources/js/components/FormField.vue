<template>
    <default-field :field="field" :errors="errors">
        <template slot="field">
            <input
                :id="field.name"
                :name="field.name"
                class="hidden"
                type="text"
                v-model="value"
            >
            <input
                @change="fileSelected"
                class="hidden"
                ref="add_file"
                type="file"
            />
            <button
                @click="$refs.add_file.click()"
                class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded"
                type="button"
            >Upload File
            </button>
            <p v-if="hasError" class="my-2 text-danger">
                {{ firstError }}
            </p>
            <div class="flex flex-col pt-8 text-center">
                <div v-for="(file, index) in files" class="w-1/2 p-2 mb-4 rounded shadow-lg"
                     style="-ms-word-wrap: break-word;word-wrap: break-word;">
                    <p><a :href="file.url" target="_blank">{{ file.originalName }}</a></p>
                    <br>
                    <p>
                        <a href="#" @click.prevent="deleteFile(index)"
                           class="text-danger hover:font-bold no-underline mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" class="feather feather-trash-2">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path
                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                <line x1="14" y1="11" x2="14" y2="17"></line>
                            </svg>
                            delete
                        </a>
                    </p>
                </div>
            </div>
        </template>
    </default-field>
</template>

<script>
    import {FormField, HandlesValidationErrors} from 'laravel-nova'

    export default {
        mixins: [FormField, HandlesValidationErrors],

        props: ['resourceName', 'resourceId', 'field'],

        data() {
            return {
                selectedFile: null,
                files: [],
            }
        },

        methods: {
            /**
             * Selected file event trigger
             */
            fileSelected(event) {
                let self = this;

                const formData = new FormData();

                formData.append('file', event.target.files[0], event.target.files[0].name);
                formData.append('disk', this.field.disk);
                formData.append('path', this.field.path);

                axios.post('/nova-vendor/array-files/upload', formData)
                    .then(responce => {
                        self.files.push(responce.data);
                        self.value = JSON.stringify(self.files);
                    })
            },

            deleteFile(index) {
                axios.delete('/nova-vendor/array-files/delete', {
                    params: {
                        file: this.files[index].name,
                        disk: this.field.disk
                    }
                }).then(responce => {
                    if (responce.data.status) {
                        this.files.splice(index, 1);
                        this.value = JSON.stringify(this.files);
                    }
                });
            },

            /*
             * Set the initial, internal value for the field.
             */
            setInitialValue() {
                this.value = this.field.value || ''
                this.files = JSON.parse(this.field.value) || []
            },

            /**
             * Fill the given FormData object with the field's internal value.
             */
            fill(formData) {
                formData.append(this.field.attribute, this.value || '')
            },

            /**
             * Update the field's internal value.
             */
            handleChange(value) {
                this.value = value
            },
        },
    }
</script>
