import{_ as i}from"./AppLayout.9db7fd5c.js";import o from"./DeleteTeamForm.4d311cf9.js";import{S as r}from"./SectionBorder.57f97476.js";import l from"./TeamMemberManager.14ae73fc.js";import n from"./UpdateTeamNameForm.55ee2099.js";import{o as m,c,w as s,a,b as t,d as p,F as d,e as f}from"./app.96a7b17b.js";import"./_plugin-vue_export-helper.cdc0426e.js";import"./Modal.bae06688.js";import"./SectionTitle.47db2a26.js";import"./ConfirmationModal.a4a5d174.js";import"./DangerButton.331de41b.js";import"./SecondaryButton.62eecfc8.js";import"./ActionMessage.bcf4f917.js";import"./DialogModal.a2621254.js";import"./FormSection.be478ea4.js";import"./TextInput.c7f3a4c2.js";import"./InputLabel.264dd785.js";import"./PrimaryButton.4f14ee60.js";const u=a("h2",{class:"font-semibold text-xl text-gray-800 leading-tight"}," Team Settings ",-1),x={class:"max-w-7xl mx-auto py-10 sm:px-6 lg:px-8"},D={__name:"Show",props:{team:Object,availableRoles:Array,permissions:Object},setup(e){return(b,h)=>(m(),c(i,{title:"Team Settings"},{header:s(()=>[u]),default:s(()=>[a("div",null,[a("div",x,[t(n,{team:e.team,permissions:e.permissions},null,8,["team","permissions"]),t(l,{class:"mt-10 sm:mt-0",team:e.team,"available-roles":e.availableRoles,"user-permissions":e.permissions},null,8,["team","available-roles","user-permissions"]),e.permissions.canDeleteTeam&&!e.team.personal_team?(m(),p(d,{key:0},[t(r),t(o,{class:"mt-10 sm:mt-0",team:e.team},null,8,["team"])],64)):f("",!0)])])]),_:1}))}};export{D as default};
