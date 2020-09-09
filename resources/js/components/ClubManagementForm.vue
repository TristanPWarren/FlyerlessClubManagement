<template>
    <b-form @submit.prevent="submit" @reset="reset">
        <div id="club-logo-image-holder">
            <div> No Image Found </div>
            <img id="club-logo-img" src=""/>
            <div id="club-logo-image"></div>
        </div>

        <b-form-group
            id="image-label"
            label-for="image"
            :description="'Select an image to be used as the logo for your club, must be of type: .png, .jpeg, .jpg or .gif ' + allowedExtensions"
        >

            <b-form-file
                v-model="file"
                id="image"
                :state="Boolean(file)"
                :placeholder="'Browse a new club logo or drag and drop one here'"
                drop-placeholder="Drop file here..."
            />
        </b-form-group>

        <b-form-group
                id="description-label"
                label-for="description"
                description="Write a description of your club"
        >
            <b-form-textarea
                    id="description"
                    v-model="description"
                    type="text"
                    placeholder="Enter text here:"
                    rows="5"
                    required
            ></b-form-textarea>
        </b-form-group>

        <b-form-group
                id="link-label"
                label-for="link"
                description="Provide a link to a Microsoft form to allow users to provide information about themselves"
        >
            <b-form-input
                    id="link"
                    v-model="link"
                    type="text"
            ></b-form-input>
        </b-form-group>

        <div v-if="canUpdate">
            <b-button type="submit" variant="primary">Update</b-button>
            <b-button type="reset" variant="danger">Reset</b-button>
        </div>
        <div v-else>
            <div id="authentication-warning"> Use as an authorised user to modify club details </div>
        </div>
    </b-form>


</template>


<script>
    export default {
        name: "ClubManagementForm",

        props: {
            allowedExtensions: {
                type: Array,
                required: false,
                default: function () {
                    return [];
                }
            },
            canUpdate: {
                type: Boolean,
                required: true,
                default: false,
            },
            queryString: {
                type: String,
                required: true,
            },

        },

        data() {
            return {
                title: '',
                description: '',
                link: '',
                file: null,
                filePath: '',
            }
        },

        mounted() {
            this.loadDescription();
        },

        methods: {
            loadDescription() {
                this.$http.get('description')
                    .then((response) => {
                        this.description = response.data.description;
                        this.link = response.data.form_link;
                        this.filePath = response.data.path_of_image;
                        this.file = null;
                        this.getLogo();
                    })
                    .catch(error => this.$notify.alert('Sorry, something went wrong retrieving files: ' + error.message));

            },
            submit() {
                let allowedFiles = ['image/gif', 'image/jpeg', 'image/png']

                if ((this.file !== null) && (allowedFiles.indexOf(this.file.type) === -1)) {
                    this.$notify.alert('File type must be of JPEG, PNG or GIF');

                } else {
                    let formData = new FormData();

                    formData.append('file[]', this.file);

                    formData.append('description', this.description);
                    formData.append('link', this.link);

                    this.$http.post('description', formData, {headers: {'Content-Type': 'multipart/form-data'}})
                        .then(response => {
                            this.$notify.success('Description Updated!');
                            this.loadDescription();
                        })
                        .catch(error => this.$notify.alert('There was a problem updating your description: ' + error.message));
                }


            },

            getLogo() {
                if (this.filePath === '') {
                    $('#club-logo-image').hide();
                } else {
                    $('#club-logo-image').css({'background-image': `url("")`});
                    $('#club-logo-image').css({'background-image': `url("${this.$url + '/' + 'club_logo?' + this.queryString}")`});
                    $('#club-logo-image').show();
                }
            },

            reset() {

                //TODO: Remove when done
                // console.log("FORM RESET");
                // this.$url + '/' + (this.isOldFiles ? 'old-file' : 'file') + '/' + id + '/download?' + this.queryString;
                // $('#club-logo-image').css({'background-image': ''});
                // console.log(this.$url + '/' + 'club_logo?' + this.queryString);
                // $('#club-logo-image').css({'background-image': `url("")`});
                // $('#club-logo-image').css({'background-image': `url("${this.$url + '/' + 'club_logo?' + this.queryString}")`});
                // this.$http.get('description').then(response => console.log(response.data)).catch(err => console.log(err));
                // this.$http.get('description').then(response => console.log(response)).catch(err => console.log(err));
                console.log("DELETE BUTTON");
                this.$http.delete('description/' + 0).then(response => console.log(response)).catch(err => console.log(err));
                // this.$http.get('club_logo').then((response) => {
                // this.$http.get('club_logo').then((response) => {
                //     console.log(response);
                //     $('#club-logo-image').css({'background-image': `url("data:image/png;base64,${response.data}")`});
                // }).catch(err => console.log(err));
                // console.log(this.$url);

                //TODO: put back in
                this.title = this.defaultDocumentTitle;
                this.file = null;
            }
        },

        computed: {}
    }
</script>

<style scoped>
#club-logo-image-holder {
    position: relative;
    width: 200px;
    height: 200px;
    border: solid black 2px;
    border-radius: 5px;
    margin-top: 20px;
    margin-bottom: 20px;
    line-height: 185px;
    text-align: center;
}

#club-logo-image {
    width: 196px;
    height: 196px;
    border-radius: 3px;
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
    position: absolute;
    top: 0px;
    left: 0px;
    background-color: white;
    z-index: 2;
}

#authentication-warning {
    color: red;
}

</style>