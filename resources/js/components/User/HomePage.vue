<template>
    <div>
        <div class="welcome-container">

            <!-- <div class="logo-wrapper">
                <img src="/img/tc_division.png" class="division-logo" alt="Division Logo">
                <img src="/img/tcnhs_logo.png" class="tcnhs-logo" alt="TCNHS Logo">
            </div>
           
            <div class="loader-3">
                <div class="circle"></div>
                <div class="circle"></div>
                <div class="circle"></div>
                <div class="circle"></div>
                <div class="circle"></div>
            </div>-->

            <div class="columns">
                <div class="column is-6">
                    <div class="buttons">
                        <b-button label="Refresh" 
                            @click="loadParkingSpaces"
                            type="is-info" icon-right="refresh"></b-button>
                    </div>
                    <div class="report-text" v-for="(park, index) in parkingSpaces" :key="index">
                        <div class="park-container">
                            <div class="p-4">
                                <div>
                                    <div class="has-text-weight-bold is-size-6">
                                        {{ park.name }}
                                    </div>
                                    <!-- {{ loadParkReservation(park.par_id) }} -->

                                    <div v-if="park.is_occupied === 0">
                                        <div class="available">AVAILABLE</div>
                                        <div>
                                            <b-button class="is-link"
                                                @click="openModalReserveMe(index)"
                                                size="is-small" label="RESERVE ME"></b-button>
                                        </div>
                                    </div>
                                    <div class="" v-else>
                                        <div class="mb-2 occupied">OCCUPIED</div>
                                        <div>
                                            <img src="/img/car.png" style="width: 250px;" alt="">
                                        </div>

                                        <div class="mb-2" v-if="park.parkReservation">
                                            <button class="button is-success mb-2"
                                                @click="displayQr(index)"
                                                v-if="(park.parkReservation.enter_time == null) && (park.parkReservation.user_id == user.user_id)">
                                                Enter Parking Space
                                            </button>

                                            <button class="button is-info mb-2"
                                                @click="extendParkingTime(park.parkReservation.park_reservation_id)"
                                                v-if="(park.parkReservation.user_id == user.user_id)">
                                                Extend Parking Time
                                            </button>
                                            <button class="button is-danger mb-2"
                                                v-if="(park.parkReservation.enter_time !== null) && (park.parkReservation.user_id == user.user_id)"
                                                @click="openModalExit(index)">
                                                Exit Parking Space
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </div> 
                </div>
            </div>
 
        </div> <!--welcome container-->


        <!--modal reserve-->
        <b-modal v-model="modalReserveMe" has-modal-card
                 trap-focus
                 :width="640"
                 aria-role="dialog"
                 aria-label="Modal"
                 aria-modal>

            <form action="/paymongo/pay">
                <div class="modal-card">
                    <header class="modal-card-head">
                        <p class="modal-card-title has-text-weight-bold is-size-5">RESERVE ME</p>
                        <button
                            type="button"
                            class="delete"
                            @click="modalReserveMe = false"/>
                    </header>

                    <section class="modal-card-body">
                        <div class="">
                            <div class="columns">
                                <div class="column">
                                   <!-- <p>To make reservation for this parking area, a payment must be made.</p>-->
                                    <p>PARKING FEE: &#8369;{{ fields.amount }}</p> 

                                    <input type="hidden" name="park" v-model="fields.row">
                                    <input type="hidden" name="user_id" v-model="user.user_id">

                                    <b-field label="Reservation From">
                                        <b-datetimepicker v-model="fields.date_time_reserve_from" editable name="date_time_reserve_from" 
                                            @input="computeAmount" placeholder="Date and Time Reservation"></b-datetimepicker>
                                    </b-field>

                                    <b-field label="Reservation To">
                                        <b-datetimepicker v-model="fields.date_time_reserve_to" editable name="date_time_reserve_to" 
                                            @input="computeAmount" placeholder="Date and Time Reservation"></b-datetimepicker>
                                    </b-field>

                                    <b-field label="No. of Hours">
                                        <b-input type="text" v-model="fields.hr" name="hours" 
                                            @input="computeAmount" readonly placeholder="1" :min="1" />
                                    </b-field>

                                    <input type="hidden" name="start" v-model="fields.date_time_reserve_from">
                                    <input type="hidden" name="end" v-model="fields.date_time_reserve_to">

                                   
                                </div>
                            </div>
                        </div>
                    </section>
                    <footer class="modal-card-foot">
                        <button
                            class="button is-primary">
                                PAY
                                <b-icon icon="arrow-right" class="ml-2"></b-icon>    
                        </button>
                    </footer>
                </div>
            </form><!--close form-->
        </b-modal>
        <!--close modal-->



        <!--modal extend time-->
        <b-modal v-model="modalExtendTime" has-modal-card
                 trap-focus
                 :width="640"
                 aria-role="dialog"
                 aria-label="Modal"
                 aria-modal>

            <form action="/paymongo/pay-extend">
                <div class="modal-card">
                    <header class="modal-card-head">
                        <p class="modal-card-title has-text-weight-bold is-size-5">EXTEND TIME</p>
                        <button
                            type="button"
                            class="delete"
                            @click="modalExtendTime = false"/>
                    </header>

                    <section class="modal-card-body">
                        <div class="">
                            <div class="columns">
                                <div class="column">
                                   <!-- <p>To make reservation for this parking area, a payment must be made.</p>-->
                                    <p>PARKING FEE: &#8369;{{ fields.amount }}</p> 

                                    <input type="hidden" name="park" v-model="fields.row">
                                    <input type="hidden" name="user_id" v-model="user.user_id">

                                    <b-field label="Extend From">
                                        <b-datetimepicker 
                                            v-model="fields.extend_from" 
                                            disabled
                                            editable 
                                            name="date_time_reserve_from" 
                                            @input="computeAmountExtend" placeholder="Date and Time Reservation"></b-datetimepicker>
                                    </b-field>

                                    <b-field label="Extend To">
                                        <b-datetimepicker v-model="fields.extend_to" 
                                            :min-datetime="fields.extend_from"
                                            editable name="date_time_reserve_to" 
                                            @input="computeAmountExtend" 
                                            placeholder="Date and Time Reservation"></b-datetimepicker>
                                    </b-field>

                                    <b-field label="No. of Hours">
                                        <b-input type="text" v-model="fields.extend_hr" name="hours" 
                                            @input="computeAmountExtend" readonly placeholder="1" :min="1" />
                                    </b-field>

                                    <input type="hidden" name="start" v-model="fields.extend_from">
                                    <input type="hidden" name="end" v-model="fields.extend_to">
                                    <input type="hidden" name="reservation_id" v-model="fields.reservation_id">

                                   
                                </div>
                            </div>
                        </div>
                    </section>
                    <footer class="modal-card-foot">
                        <button
                            class="button is-primary">
                                PAY
                                <b-icon icon="arrow-right" class="ml-2"></b-icon>    
                        </button>
                    </footer>
                </div>
            </form><!--close form-->
        </b-modal>
        <!--close modal-->

        <b-modal v-model="modalQR" has-modal-card
                 trap-focus
                 :width="640"
                 aria-role="dialog"
                 aria-label="Modal"
                 aria-modal>
                <div class="modal-card">
                    <header class="modal-card-head">
                        <p class="modal-card-title has-text-weight-bold is-size-5">RESERVATION QR</p>
                        <button
                            type="button"
                            class="delete"
                            @click="loadParkingSpaces"/>
                    </header>

                    <section class="modal-card-body">
                        <div class="">
                            <div class="columns">
                                <div class="column">
                                    <div class="qr">
                                        <qrcode :value="qr" :options="{ width: 400 }"></qrcode>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </section>
                    <footer class="modal-card-foot">
                        <button
                            @click="loadParkingSpaces"
                            class="button is-primary">
                                Done
                                <b-icon icon="arrow-right" class="ml-2"></b-icon>    
                        </button>
                    </footer>
                </div>
        </b-modal>

        <b-modal v-model="confirmExit" has-modal-card
                 trap-focus
                 :width="640"
                 aria-role="dialog"
                 aria-label="Modal"
                 aria-modal>
                <div class="modal-card">
                    <header class="modal-card-head">
                        <p class="modal-card-title has-text-weight-bold is-size-5">Confirmation</p>
                        <button
                            type="button"
                            class="delete"
                            @click="confirmExit = false"/>
                    </header>

                    <section class="modal-card-body">
                        <div class="">
                            <div class="columns">
                                <div class="column">
                                    <div style="color:red; margin-bottom: 10px;">{{excessMsg}}</div>
                                    <div>Are you sure you want to exit park?</div>
                                    
                                </div>
                            </div>
                        </div>
                    </section>
                    <footer class="modal-card-foot">
                        <button
                            @click="confirmExit = false"
                            class="button is-secondary">
                            No
                        </button>
                        <button
                            @click="exitParking"
                            :class="btnClassPrimary">
                                Yes   
                        </button>
                    </footer>
                </div>
        </b-modal>


    </div>
</template>

<script>
export default {

    props: {
        propsParkPrice: {
            type: Object,
            default: ()=> {}
        }
    },

 
	data(){

        const currentDate = new Date();
    
		return{
            btnClassPrimary: {
                'button': true,
                'is-primary': true,
                'is-loading': false
            },
            info: {},
            parkingSpaces: [],
            reports: [],
            user: [],

            modalReserveMe: false,
            modalExtendTime: false,
            modalQR: false,
            confirmExit: false,
            errors: {},
            fields: {
                date_time_reserve_from: currentDate,
                date_time_reserve_to: new Date(currentDate.getTime() + (1 * 60 * 60 * 1000)), // Add 1 hour

                hr:1,
                amount:20,

                reservation_id: 0,
                extend_hr: 1,
                extend_from: null,
                extend_to: null,
                extend_amount: 20
            },
            qr: null,

            excessMsg: '',
            isTImeExcess: false,
		}
	},

	methods:{
        loadParkingSpaces(){
            axios.get('/load-parking-spaces').then(res=>{
                this.parkingSpaces = res.data
                this.modalQR = false
                
            }).catch(err=>{
            
            })
        },
        loadProfile(){
            axios.get('/load-profile').then(res=>{
                this.user = res.data;
                
            }).catch(err=>{
            
            })
        },

        openModalReserveMe(row){
            this.fields.row = row
            this.modalReserveMe = true
        },
        openModalExit(row){
            const parkPrice = this.propsParkPrice.park_price;
            this.fields.row = this.parkingSpaces[row].parkReservation.park_reservation_id;
            const parkReservationId = this.parkingSpaces[row].parkReservation.park_reservation_id;


            if(this.parkingSpaces[row].parkReservation.end_time){
                
                const endTime  = this.parkingSpaces[row].parkReservation.end_time;
                const endDate = new Date(endTime.replace(/-/g, '/')); // JavaScript expects date in the format 'YYYY/MM/DD'
                const currentDate = new Date();
                const timeDifference = currentDate - endDate;
                const hoursExcess = timeDifference / (1000 * 60 * 60);

                const roundedHours = Math.ceil(hoursExcess);
                const fines = (roundedHours * parkPrice);
                if (hoursExcess > 0) {
                    this.excessMsg = `The current time is ${roundedHours} hours past the scheduled end time. A fine of ${fines} pesos must paid before exiting.`;
                    this.isTImeExcess = true
                }else{
                    this.excessMsg = ''
                    this.isTImeExcess = false
                }

            }
            

            this.fields.row = this.parkingSpaces[row].parkReservation.park_reservation_id;
            this.confirmExit = true
        },
        exitParking(){

            this.btnClassPrimary['is-loading'] = true


            axios.post('/exit-park/'+this.fields.row).then(
                res=>{
                    this.confirmExit = false;
                    this.btnClassPrimary['is-loading'] = false

                    this.loadParkingSpaces();
                    if(res.data.status === 'updated'){
                            this.$buefy.toast.open({
                            duration: 5000,
                            message: `Thank you. You may exit your vehicle now.`,
                            position: 'is-bottom',
                            type: 'is-success'
                        })
                    }

                    if(res.data.status === 'penalty'){
                        window.location = '/paymongo/exit-park/' + this.fields.row
                    }
                }
            )
        },
        computeAmount(){
            var a = new Date(this.fields.date_time_reserve_to);
            var b = new Date(this.fields.date_time_reserve_from);
            var hours = this.roundNum(Math.abs(b - a) / 36e5);

            //this.fields.amount = this.fields.hr * 20
            this.fields.hr = hours
            const parkPrice = this.propsParkPrice.park_price
            this.fields.amount = this.roundNum(hours * parkPrice)
        },

        computeAmountExtend(){
            var a = new Date(this.fields.extend_from);
            var b = new Date(this.fields.extend_to);
            var hours = this.roundNum(Math.abs(b - a) / 36e5);

            //this.fields.amount = this.fields.hr * 20
            this.fields.extend_hr = hours
            const parkPrice = this.propsParkPrice.park_price

            this.fields.extend_amount = this.roundNum(hours * parkPrice)
        },

        roundNum(num){
           return Math.round((num + Number.EPSILON) * 100) / 100;
        },

        displayQr(index){
            this.qr = this.parkingSpaces[index].parkReservation.qr_ref;
            this.modalQR = true;
        },

        // this will extend parking time
        extendParkingTime(id){
            this.reservation_id = 0;
            axios.get('/get-my-reservation/' + id).then(res=>{
                this.fields.reservation_id = id;
                this.fields.extend_from = new Date(res.data.end_time);

                //add 1 hour from expire time date
                this.fields.extend_to = new Date(this.fields.extend_from + (1 * 60 * 60 * 1000))

                this.modalExtendTime = true
                //console.log(res.data)
            }).catch(err=>{
            
            })
            
        }
        
        
	},
    computed:{
        qrCode(){
            return this.parkReserved.qr_ref;
        }
    },

    mounted() {
    
    this.loadParkingSpaces();
    this.fields;
    this.loadProfile();
    }
}
</script>
<!-- 
<style scoped src="../../../css/admin-home.css">
</style> -->

<style scoped>
.welcome-container{
    height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
 
}


.park-container{
    border-top: 2px solid #000; /* Set the top border */
    border-left: 2px solid #000; /* Set the left border */
    border-bottom: 2px solid #000; /* Set the bottom border */
    border-right: none; /* No border on the right */
    margin-bottom: 10px;
    /*height: 220px; */
    min-width: 300px;
}


.occupied{
    color: red;
    font-weight: bolder;

}
.available{
    color: green;
    font-weight: bolder;
}
.qr{
    display: flex;
    justify-content: center;
}

</style>
