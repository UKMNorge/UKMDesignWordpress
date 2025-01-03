<template>
    <div class="as-container container as-card-1 as-padding-space-6 nosh-impt">
        <!-- Finner arrangementer nær deg... -->
        <div v-if="locationLoading || !fetchedArrangementer">
            <h1 class="as-text-center">Finner arrangementer nær deg...</h1>
            <div class="as-display-flex as-margin-top-space-6">
                <div class="as-margin-auto">
                    <LoadingComponent />
                    <div class="as-margin-top-space-5">
                        <div class="as-margin-top-space-4 as-display-flex">
                            <div v-if="error" class="as-margin-auto">
                                <WriteChatComponent :textToShow="'Vi klarte dessverre ikke å få tilgang til din lokasjon'" :interval="50"/>
                                <WriteChatComponent :textToShow="'Videresender deg til alle kommuner!'" :interval="50" :timeout="tilAlleFylkerTimeout*0.4"/>
                            </div>
                            <div v-else class="as-margin-auto">
                                <WriteChatComponent v-if="gettingKommune" :textToShow="'Henter informasjon om din kommune.'" :interval="50"/>
                                <WriteChatComponent v-else-if="locationLoading" :textToShow="'Tillat tilgang til din lokasjon for å finne arrangementer nær deg'" :interval="50"/>
                                <WriteChatComponent v-else :textToShow="'Henter arrangementer'" :interval="100"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tilgjengelige arrangementer -->
        <div v-if="!locationLoading && fetchedArrangementer && tilgjengeligeArrangementer.length > 0">
            <div>
                <h1 v-if="tilgjengeligeArrangementer.length == 1" class="as-text-center">Skal du delta i {{ kommune }} kommune?</h1>
                <h1 v-else class="as-text-center">Arrangementer nær deg</h1>
            </div>
            <div class="as-margin-top-space-6">
                <div class="as-margin-top-space-2 as-display-flex" v-for="arrangement in tilgjengeligeArrangementer" :key="arrangement.id">
                    <button @click="arrangementClicked(arrangement)" class="event-box as-margin-auto as-padding-space-3 as-btn-default as-btn-hover-default as-card-1">
                        <div class="float-left overunder mr-3" style="align-self: center; border-right: solid 2px #A0AEC0; padding-right: 16px;">
                            <div class="over" style="font-size: .7em;">
                                {{ getDag(arrangement.dato, true).toUpperCase() }}
                            </div>
                            <div class="under" style="font-size:1.4em;">
                                {{ arrangement.dato.toLocaleString('en-US', { day: '2-digit' }) }}
                            </div>
                            <div class="under" style="font-size: .7em; margin-top: 0.2em; white-space: nowrap;">
                                {{ getMaaned(arrangement.dato, true) }}
                                {{ arrangement.dato.getFullYear() }}
                            </div>
                        </div>
                        <h3 style="align-self: center; margin-right: 10px;">
                            {{ arrangement.navn }}
                        </h3>
                        <div class="icon-arrangement-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path></svg>
                        </div>
                    </button>
                </div>
            </div>
            
            <div class="as-margin-top-space-8 as-display-flex">
                <button @click="userToAlleFylker(true)" v-if="tilgjengeligeArrangementer.length == 1" class="as-btn-simple as-margin-auto as-btn-hover-default default">Leter du ikke etter {{ tilgjengeligeArrangementer[0].navn }}?</button>
                <button @click="userToAlleFylker(true)" v-else class="as-btn-simple as-margin-auto as-btn-hover-default default">Finner du ikke arrangementet i listen?</button>
            </div>
            
        </div>
        <!-- Ingen arrangementer i nærheten -->
        <div v-else>
            <div v-if="!locationLoading && fetchedArrangementer">
                <h1 class="as-text-center">Vi fant ingen arrangementer i nærheten!</h1>
                <div class="as-margin-top-space-4 as-display-flex">
                    <div class="as-margin-auto">
                        <WriteChatComponent :textToShow="'På neste side kan velge din kommune for å finne arrangementer'" :interval="50" />
                    </div>
                </div>
                <div class="as-margin-top-space-2 as-display-flex">
                    <div class="as-margin-auto">
                        <WriteChatComponent :textToShow="'Videresender deg til finn UKM der du bor!'" :interval="50" :timeout="tilAlleFylkerTimeout*0.4"/>
                    </div>
                </div>
            </div>
        </div>    
    </div>
</template>

<script lang="ts">
import FirstTab from './tabs/FirstTab.vue';
import LoadingComponent from './components/LoadingComponent.vue';
import { SPAInteraction } from 'ukm-spa/SPAInteraction';
import { Director } from 'ukm-spa/Director';
import { ref, onMounted } from 'vue'
import WriteChatComponent from './components/WriteChatComponent.vue';
var ukmHostname : string = (<any>window).UKM_HOSTNAME; // Kommer fra global
var UKM : string = (<any>window).UKM; // Kommer fra global


export default {

    data() {
        return {
            locationLoading: true as boolean,
            gettingKommune: false as boolean,
            kommune: '' as string,
            kommunenummer: 0 as number,
            name : "World" as String,
            activeTab : 'first' as String,
            tilgjengeligeArrangementer : [] as Array<any>,
            tilAlleFylkerTimeout : 4000 as number,
            fetchedArrangementer : false as boolean,
            error : false as boolean
        }
    },

    components : {
        FirstTab : FirstTab,
        LoadingComponent : LoadingComponent,
        WriteChatComponent : WriteChatComponent
    },

    mounted: function () {
        console.log('initialized');
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
            this.$data.locationLoading = false;
            this.$data.error = true;
            this.userToAlleFylker();
        },
        async getKommuneFraGeoNorge(latitude : number, longitude : number) {
            this.$data.gettingKommune = true;
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
            this.$data.gettingKommune = false;
            const url = ukmHostname == 'ukm.no' ? 'https://api.ukm.no/public:arrangement-finder/' : 'https://'+ ukmHostname +'/wp-content/themes/UKMDesignWordpress/ajax/finn_arrangement.ajax.php';
            const spaInteraction = new SPAInteraction(null, url);
            var data = {
                kommune: kommune,
                kommunenummer: kommunenummer
            };

            var response = await spaInteraction.runAjaxCall('', 'POST', data);
        

            for(var arrangement of response.arrangementer) {
                const isoDateString = arrangement.dato.date.replace(' ', 'T');
                arrangement.dato = new Date(isoDateString);
                this.$data.tilgjengeligeArrangementer.push(arrangement);
            }

            if(response.arrangementer) {
                this.$data.fetchedArrangementer = true;
            }
            if(response.arrangementer.length < 1) {
                this.userToAlleFylker();
            }            
        },
        // Functions to be moved to a separate file
        getMaaned(date : Date, isShort : boolean = false) {
            const monthNames =  ['Januar', 'Februar', 'Mars', 'April', 'Mai', 'Juni',
                'Juli', 'August', 'September', 'Oktober', 'November', 'Desember'
            ];
            const monthNamesShort = ['Jan', 'Feb', 'Mar', 'Apr', 'Mai', 'Jun',
                'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Des'
            ];

            if(isShort) {
                return monthNamesShort[date.getMonth()];
            }
            return monthNames[date.getMonth()];
        },
        getDag(date : Date, isShort : boolean = false) {
		    const dayNames = ['Søndag','Mandag','Tirsdag','Onsdag','Torsdag','Fredag','Lørdag']
            const dayNamesShort = ['Søn','Man','Tir','Ons','Tor','Fre','Lør'];

            if(isShort) {
                return dayNamesShort[date.getDay()];
            }

            return dayNames[date.getDay()];
        },
        // Clicks
        userToAlleFylker(noTimeout : boolean = false) {
            if(noTimeout) {
                window.location.href = "/din_monstring/";
                return;
            }
            
            setTimeout(() => {
                window.location.href = "/din_monstring/";
            }, this.$data.tilAlleFylkerTimeout);
        },
        arrangementClicked(arrangement : any) {
            window.location.href = arrangement.url;
        }
    }
}
</script>

<style scoped>
.event-box {
    background: var(--color-primary-bla-25);
    max-width: 600px;
    min-width: 35vw;
    margin: auto !important;
}
.icon-arrangement-btn {
    margin: auto;
    margin-right: 0;
}
.as-container {
    background: var(--color-primary-grey-extra-lightes);
}

/* CSS for screens smaller than 600px */
@media (max-width: 600px) {
    .event-box {
        min-width: 100%;
    }
}


</style>



