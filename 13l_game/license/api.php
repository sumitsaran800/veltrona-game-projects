<?php
require_once __DIR__ . '/_core.php';
$conn=ol_ensure(); $d=ol_json(); $action=(string)($d['action']??''); $domain=ol_domain($d['domain']??($_SERVER['HTTP_ORIGIN']??'')); $key=trim((string)($d['licenseKey']??''));
if($action==='chat_send'){
  $name=substr((string)($d['name']??''),0,180); $contact=substr((string)($d['contact']??''),0,180); $msg=substr((string)($d['message']??''),0,2000);
  $stmt=$conn->prepare('INSERT INTO owner_license_chat(domain,license_key,name,contact,message) VALUES(?,?,?,?,?)'); if($stmt){$stmt->bind_param('sssss',$domain,$key,$name,$contact,$msg);$stmt->execute();}
  header('Content-Type: application/json'); echo json_encode(['ok'=>true,'message'=>'Message sent']); exit;
}
if($action==='domain_check'){
  $domain2=preg_replace('/^www\./','',$domain);
  $stmt=$conn->prepare('SELECT * FROM owner_domain_whitelist WHERE (domain=? OR domain=?) AND status=1 ORDER BY IF(domain=?,1,0) DESC LIMIT 1');
  $stmt->bind_param('sss',$domain,$domain2,$domain); $stmt->execute(); $wl=$stmt->get_result()->fetch_assoc();
  if($wl){
    $lk=trim((string)($wl['license_key']??''));
    $autoDays=max(1,(int)($wl['auto_days']??30));
    $valid=!empty($wl['valid_until']) ? $wl['valid_until'] : date('Y-m-d H:i:s', strtotime('+'.$autoDays.' days'));
    $cust=($wl['customer_name']??'') ?: $domain;
    if(!$lk){
      $lk=ol_make_key(); $status='active'; $note='Auto from whitelist';
      $st=$conn->prepare('INSERT INTO owner_licenses(license_key,customer_name,domain,status,valid_until,note) VALUES(?,?,?,?,?,?)');
      if($st){$st->bind_param('ssssss',$lk,$cust,$domain,$status,$valid,$note);$st->execute();}
      $up=$conn->prepare('UPDATE owner_domain_whitelist SET license_key=?,valid_until=?,updated_at=NOW() WHERE id=?'); $id=(int)$wl['id']; if($up){$up->bind_param('ssi',$lk,$valid,$id);$up->execute();}
    }
    $rs=$conn->query("SELECT * FROM owner_licenses WHERE license_key='".$conn->real_escape_string($lk)."' LIMIT 1"); $lic=$rs?$rs->fetch_assoc():null;
    if($lic && !empty($valid) && (empty($lic['valid_until']) || $lic['valid_until']!==$valid)){
      $id=(int)$lic['id']; $status='active'; $st=$conn->prepare('UPDATE owner_licenses SET valid_until=?,status=?,updated_at=NOW() WHERE id=?'); if($st){$st->bind_param('ssi',$valid,$status,$id);$st->execute();$lic['valid_until']=$valid;$lic['status']=$status;}
    }
    if($lic) ol_reply(ol_license_payload($conn,$lic,$domain,(string)($wl['message']??'License active')));
  }
  ol_reply(['status'=>'inactive','domain'=>$domain,'message'=>'Domain is not whitelisted']);
}
if($action==='activate'){
  if(!$key) ol_reply(['status'=>'inactive','domain'=>$domain,'message'=>'License key required']);
  $stmt=$conn->prepare('SELECT * FROM owner_licenses WHERE license_key=? LIMIT 1'); $stmt->bind_param('s',$key); $stmt->execute(); $lic=$stmt->get_result()->fetch_assoc();
  if(!$lic) ol_reply(['status'=>'inactive','domain'=>$domain,'message'=>'Invalid license key']);
  if($lic['status']==='blocked') ol_reply(ol_license_payload($conn,$lic,$domain,'License blocked by owner'));
  if(!empty($lic['valid_until']) && strtotime($lic['valid_until']) < time()) { $lic['status']='expired'; ol_reply(ol_license_payload($conn,$lic,$domain,'License expired. Contact owner.')); }
  $bound=ol_domain($lic['domain']??'');
  if($bound && $bound!==$domain) ol_reply(['status'=>'inactive','licenseKey'=>$key,'domain'=>$domain,'message'=>'This key is already bound with '.$bound]);
  if(!$bound){ $stmt=$conn->prepare('UPDATE owner_licenses SET domain=?,updated_at=NOW() WHERE id=?'); $id=(int)$lic['id']; $stmt->bind_param('si',$domain,$id); $stmt->execute(); $lic['domain']=$domain; }
  $lic['status']='active'; ol_reply(ol_license_payload($conn,$lic,$domain,'License active'));
}
if($action==='check'){
  $lic=null;
  if($key){ $stmt=$conn->prepare('SELECT * FROM owner_licenses WHERE license_key=? LIMIT 1'); $stmt->bind_param('s',$key); $stmt->execute(); $lic=$stmt->get_result()->fetch_assoc(); }
  if(!$lic && $domain){ $stmt=$conn->prepare('SELECT * FROM owner_licenses WHERE domain=? LIMIT 1'); $stmt->bind_param('s',$domain); $stmt->execute(); $lic=$stmt->get_result()->fetch_assoc(); }
  if(!$lic) ol_reply(['status'=>'inactive','domain'=>$domain,'message'=>'License not found']);
  if(!empty($lic['valid_until']) && strtotime($lic['valid_until']) < time()) $lic['status']='expired';
  if($lic['status']==='blocked') ol_reply(ol_license_payload($conn,$lic,$domain,'License blocked by owner'));
  if(ol_domain($lic['domain']??'') && ol_domain($lic['domain'])!==$domain) ol_reply(['status'=>'inactive','domain'=>$domain,'message'=>'Domain mismatch']);
  ol_reply(ol_license_payload($conn,$lic,$domain,$lic['status']==='active'?'License active':'License inactive'));
}
ol_reply(['status'=>'inactive','domain'=>$domain,'message'=>'Unknown action']);
?>