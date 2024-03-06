<template>
    <div class="as-container container">
        <button class="as-btn-simple as-margin-right-space-3 as-btn-hover-default btn-with-icon">Finn arrangement</button>
        <p>{{ locationLoading }}</p>
        <p v-if="locationLoading">Loading...</p>
        <p v-if="!locationLoading">NOT Loading...</p>
        <p>{{ kommune }}</p>
        <p>{{ kommunenummer }}</p>
        <!-- <div class="tab-button">
            <button @click="openTab('first')">First Tab</button>
            <button @click="openTab('second')">Second Tab</button>
        </div> -->

     
    </div>
</template>

<script lang="ts">
import FirstTab from './tabs/FirstTab.vue';
import { SPAInteraction } from 'ukm-spa/SPAInteraction';
import { Director } from 'ukm-spa/Director';
import { ref, onMounted } from 'vue'
var ukmHostname : string = (<any>window).UKM_HOSTNAME; // Kommer fra global
var UKM : string = (<any>window).UKM; // Kommer fra global


export default {

    data() {
        return {
            locationLoading: true as boolean,
            kommune: '' as string,
            kommunenummer: 0 as number,
            name : "World" as String,
            activeTab : 'first' as String
        }
    },

    components : {
        FirstTab : FirstTab
    },

    mounted: function () {
        this.initArrangementFinder();
    },
    
    methods: {
        initArrangementFinder() {
            this.getLocation();
        },
        getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(this.showPosition, this.showError);
            } else {
                console.log("Geolocation is not supported by this browser.");
            }
        },
        showPosition(position : {coords : {latitude : number, longitude : number}}) {
            var latitude = position.coords.latitude;
            var longitude = position.coords.longitude;
            this.$data.locationLoading = false;
            this.getKommuneFraGeoNorge(latitude, longitude);
        },
        showError(error : any) {
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    alert("User denied the request for Geolocation.");
                break;
                case error.POSITION_UNAVAILABLE:
                    alert("Location information is unavailable.");
                break;
                case error.TIMEOUT:
                    alert("The request to get user location timed out.");
                break;
                case error.UNKNOWN_ERROR:
                    alert("An unknown error occurred.");
                break;
            }
        },
        async getKommuneFraGeoNorge(latitude : number, longitude : number) {
            var spaInteraction = new SPAInteraction(null, 'https://ws.geonorge.no/kommuneinfo/v1/punkt?' +
                'nord=' + latitude +
                '&ost=' + longitude +
                '&koordsys=4258');
            
            var response = await spaInteraction.runAjaxCall('', 'GET', {});
            this.$data.kommune = response.kommunenavn;
            this.$data.kommunenummer = response.kommunenummer;

            this.getArrangementer(response.kommunenavn, response.kommunenummer);
        },
        async getArrangementer(kommune : string, kommunenummer : number) {

            const spaInteraction = new SPAInteraction(null, 'https://'+ ukmHostname +'/wp-content/themes/UKMDesignWordpress/ajax/');
            var data = {
                action: 'UKMrapporter_ajax',
                controller: 'finn_arrangement',
                testArg: 'test',
                kommune: kommune,
                kommunenummer: kommunenummer
            };
            console.log(ukmHostname);

            var response = await spaInteraction.runAjaxCall('finn_arrangement.ajax.php', 'POST', data);
            
        }
    }
}
</script>




